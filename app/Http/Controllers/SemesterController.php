<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Lecturer;
use App\Models\Semester;
use App\Models\Timetable;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    protected $className = 'App\Models\Semester';
    protected $entityName = 'semesters';
    protected $fields = ['name', 'start_date', 'end_date'];
    protected $validation = [
        'name' => 'required',
        'start_date' => 'required',
        'end_date' => 'required'
    ];

    // Start process of sending notifications to lecturers
    public function getRun(Request $req, $id)
    {
        // check if notification is already sent
        $notification = Notification::where('semester_id', $id)->first();

        // if notification object is not found
        if (!$notification) {
            // get all lecturers who are active
            $lecturers = Lecturer::where('active', true)->get();

            // loop through all lecturers
            foreach ($lecturers as $lecturer) {
                // create notification object
                $notification = new Notification();
                $notification->semester_id = $id;
                $notification->lecturer_id = $lecturer->id;
                $notification->kind = 1;
                $notification->done = false;
                $notification->save();
            }

            return redirect('semesters/index')->with('success', 'Der Durchlauf wurde gestartet. Es kann einige Zeit dauern, bis alle Benachrichtigungen versendet wurden.');
        } else {
            return redirect('semesters/index')->with('error', 'Der Durchlauf wurde bereits gestartet');
        }
    }

    public function showSemesterTimetables($semesterId)
    {
        $semester = Semester::findOrFail($semesterId);
        $timetables = Timetable::where('semester_id', $semester->id)->with('lecturer')->get();

        return view('semesters.timetables', compact('semester', 'timetables'));
    }
}
