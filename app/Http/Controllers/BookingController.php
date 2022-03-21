<?php

namespace App\Http\Controllers;

use App\Models\Doctors;
use App\Models\OperationTheatres;
use App\Models\Pateints;
use App\Models\Reservations;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class BookingController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set('Asia/Kolkata');
    }
    public function index()
    {
        return view('bookings');
    }
    public function theaterDetails(Request $request)
    {
        $data = [];
        $validated = $request->validate([
            'start' => 'required',
            'otDuration' => 'required',
            'otStart' => 'required',
        ]);
        if (!empty($validated)) {
            $opDate = date('Y-m-d H:i:s', strtotime($validated['start'] . ' ' . $validated['otStart']));
            $carbDate =  Carbon::parse($opDate);
            $opEndDateTime = $carbDate->addHour($validated['otDuration'])->toDateTimeString();
            if ($opDate > date('Y-m-d H:i:s') && $opEndDateTime >=  date('Y-m-d H:i:s') && $opDate != $opEndDateTime && $opDate < $opEndDateTime) {
                $data['doctors'] = Doctors::select('doctors.id', 'doctors.name')
                    ->leftJoin('reservations', 'reservations.doctor_id', 'doctors.id')
                    ->whereRaw(
                        "( Concat_ws(' ', ot_start_date, ot_start_time)  NOT BETWEEN  '$opDate' AND '$opEndDateTime'  AND Concat_ws(' ', ot_end_date, ot_end_time)  NOT BETWEEN  '$opDate' AND '$opEndDateTime'  AND ( Concat_ws(' ', ot_start_date, ot_start_time)  < '$opDate' AND Concat_ws(' ', ot_end_date, ot_end_time)   <=  '$opEndDateTime' )  )"
                    )->where('doctors.is_surgeon', 1)
                    ->where('doctors.is_active', 1)->groupBy('doctors.id', 'doctors.name')->get()->toArray();
                $data['pateints'] = Pateints::select('pateints.id', 'pateints.name')
                    ->leftJoin('reservations', 'reservations.pateint_id', 'pateints.id')
                    ->whereRaw(
                        "( Concat_ws(' ', ot_start_date, ot_start_time)  NOT BETWEEN  '$opDate' AND '$opEndDateTime'  AND Concat_ws(' ', ot_end_date, ot_end_time)  NOT BETWEEN  '$opDate' AND '$opEndDateTime'  AND ( Concat_ws(' ', ot_start_date, ot_start_time)  < '$opDate' AND Concat_ws(' ', ot_end_date, ot_end_time)   <=  '$opEndDateTime' )  )"
                    )->where('pateints.is_admitted', 1)
                    ->where('pateints.is_active', 1)->groupBy('pateints.id', 'pateints.name')
                    ->get()->toArray();
                $data['otRomms'] = OperationTheatres::select('operation_theatres.id', 'operation_theatres.room_no')
                    ->leftJoin('reservations', 'reservations.room_id', 'operation_theatres.id')
                    ->whereRaw(
                        "( Concat_ws(' ', ot_start_date, ot_start_time)  NOT BETWEEN  '$opDate' AND '$opEndDateTime'  AND Concat_ws(' ', ot_end_date, ot_end_time)  NOT BETWEEN  '$opDate' AND '$opEndDateTime'  AND ( Concat_ws(' ', ot_start_date, ot_start_time)  < '$opDate' AND Concat_ws(' ', ot_end_date, ot_end_time)   <=  '$opEndDateTime' )  )"
                    )
                    ->where('operation_theatres.is_active', 1)->groupBy('operation_theatres.id', 'operation_theatres.room_no')
                    ->get()->toArray();
                if (!empty($data['doctors']) && !empty($data['pateints']) && !empty($data['otRomms'])) {
                    return response()->json($data);
                } else {
                    return response()->json(array());
                }
            }
        }
        return response()->json($data);
    }
    public function getEvent()
    {
        if (request()->ajax()) {
            $events = [];
            $start = (!empty($_GET["start"])) ? ($_GET["start"]) : ('');
            $end = (!empty($_GET["end"])) ? ($_GET["end"]) : ('');
            $bookings = Reservations::whereDate('ot_start_date', '>=', $start)
                ->whereDate('ot_end_date',   '<=', $end)
                ->where('is_ot_confirm', 1)
                ->where('is_active', 1)
                ->get();
            if (!empty($bookings)) {
                foreach ($bookings as $booking) {
                    $docName = @$booking->doctor_details[0]['name'];
                    $pateintName = @$booking->pateint_details[0]['name'];
                    $otRoom = @$booking->otRoom_details[0]['room_no'];
                    $title = date('h:i a', strtotime($booking->ot_start_time)) . " -  " . date('h:i a', strtotime($booking->ot_end_time)) . " Dr. $docName booked room no $otRoom for $pateintName ";
                    $bgColor = '#ffc107';
                    if (strtotime($booking->ot_start_date) == strtotime(date('Y-m-d'))) {
                        $bgColor = '#198754';
                    } else  if (strtotime($booking->ot_start_date . " " . $booking->ot_start_time) > strtotime(date('Y-m-d H:i:s'))) {
                        $bgColor = '#0d6efd';
                    }
                    $events[] = array(
                        'id' => base64_encode($booking->id),
                        'title' => $title,
                        'backgroundColor' => $bgColor,
                        'allDay' => true,
                        'start' => date('Y-m-d H:i:s', strtotime($booking->ot_start_date . " " . $booking->ot_start_time)),
                        'end' => date('Y-m-d H:i:s', strtotime($booking->ot_end_date . " " . $booking->ot_end_time)),
                    );
                }
            }
            return response()->json($events);
        }
        return view('bookings');
    }
    public function createBooking(Request $request)
    {
        $validated = $request->validate([
            'otDate' => 'required',
            'otDuration' => 'required',
            'otStart' => 'required',
            'otDoctor' => 'required',
            'otPateint' => 'required',
            'otRoom' => 'required',
        ]);

        if (!empty($validated)) {
            $opDate = date('Y-m-d H:i:s', strtotime($validated['otDate'] . ' ' . $validated['otStart']));
            $carbDate =  Carbon::parse($opDate);
            $opEndDateTime = $carbDate->addHour($validated['otDuration'])->toDateTimeString();
            $opEndDate = date('Y-m-d', strtotime($opEndDateTime));
            $opEndTime = date('H:i:s', strtotime($opEndDateTime));
            if ($opDate > date('Y-m-d H:i:s') && $opEndDateTime >=  date('Y-m-d H:i:s') && $opDate != $opEndDateTime && $opDate < $opEndDateTime) {
                $reserved =  Reservations::whereRaw(
                    "( Concat_ws(' ', ot_start_date, ot_start_time) BETWEEN '$opDate' AND '$opEndDateTime'  OR Concat_ws(' ', ot_end_date, ot_end_time)   BETWEEN  '$opDate' AND '$opEndDateTime')"
                )->where('reservations.is_ot_confirm', 1)
                    ->where(
                        fn ($q) => $q->where('reservations.doctor_id', $validated['otDoctor'])
                            ->orWhere('reservations.pateint_id',  $validated['otPateint'])
                            ->orWhere('reservations.room_id',  $validated['otRoom'])
                    )->get()->toArray();
                if (empty($reserved)) {
                    Reservations::insert([[
                        'doctor_id' => $validated['otDoctor'],
                        'pateint_id' => $validated['otPateint'],
                        'room_id' => $validated['otRoom'],
                        'ot_start_date' => $validated['otDate'],
                        'ot_start_time' => $validated['otStart'],
                        'ot_end_date' => $opEndDate,
                        'ot_end_time' => $opEndTime,
                        'ot_duration' => $validated['otDuration'],
                        'ot_status' => 1,
                        'is_ot_confirm' => 1,
                        'is_active' => 1,
                        'bookingTime' => Carbon::now(),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]]);
                }
            }
        }
        return redirect('/');
    }

    public function deleteEvent(Request $request)
    {
        $event_id = base64_decode($request->id);
        $event = Reservations::find($event_id);
        if (!empty($event)) {
            $start = date('Y-m-d H:i:s', strtotime($event['ot_start_date'] . ' ' . $event['ot_start_time']));
            $end = date('Y-m-d H:i:s', strtotime($event['ot_end_date'] . ' ' . $event['ot_end_time']));
            if ($start > date('Y-m-d H:i:s') && $end > date('Y-m-d H:i:s')) {
                return $event->delete();
            }
        }
        return 0;
    }
}