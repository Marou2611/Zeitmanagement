<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Semester;
use App\Models\Timetable;
use Illuminate\Http\Request;

class LecturerController extends Controller
{
    protected $className = 'App\Models\Lecturer';
    protected $entityName = 'lecturers';
    protected $fields = ['firstname', 'lastname', 'email', 'phone'];
    protected $validation = [
        'firstname' => 'required',
        'lastname' => 'required',
        'email' => 'required|email',
        'phone' => 'required'
    ];

    public function getTimetable(Request $req)
    {
        $lecturer = Lecturer::find($req->route('lecturer'));

        $semester = Semester::find($req->route('semester'));

        $timetables = Timetable::where('lecturer_id', $lecturer->id)
            ->where('semester_id', $semester->id)
            ->get();

        // get previous date from last semester end date
        $previousSemester = Semester::where('end_date', '<', $semester->start_date)
            ->orderBy('end_date', 'desc')
            ->first();

        if (!$previousSemester) {
            $previousTimetables = [];
        } else {
            $previousTimetables = Timetable::where('lecturer_id', $lecturer->id)
                ->where('semester_id', $previousSemester->id)
                ->get();
        }

        return view('timetables.index')->with('timetables', $timetables)
            ->with('lecturer', $lecturer)
            ->with('semester', $semester)
            ->with('previousTimetable', $previousTimetables[0] ?? []);
    }

    public function postSave(Request $req)
    {
        $req->validate($this->validation);
        $class = $this->className;
        $entity = new $class();
        foreach ($this->fields as $field) {
            $entity->$field = $req->input($field);
        }
        $entity->save();

        return redirect($this->entityName . '/index')->with('success');
    }
}
