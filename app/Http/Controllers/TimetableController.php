<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Semester;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use App\Models\Timetable;


class TimetableController extends Controller
{
    protected $className = 'App\Models\Timetable';
    protected $entityName = 'timetables';
    protected $fields = [
        'teaching_load',        // Aktueller Lehrumfang in SWS
        'max_hours_per_day',    // Maximale SWS-Zahl pro Tag
        'use_cn0003',           // Nutzung CN0003 für Lehrveranstaltung
        'use_group_rooms',      // Nutzung Gruppentischräume/Stuhlkreis für
        'comments',             // Anmerkungen
        'bachelor',             // Zeiten Bachelor
        'master',               // Zeiten Master
    ];
    protected $validation = [
        'teaching_load' => 'required|numeric',
        'max_hours_per_day' => 'required|numeric',
        'use_cn0003' => 'sometimes|boolean',
        'use_group_rooms' => 'sometimes|boolean',
        'comments' => 'sometimes',
        'bachelor' => 'required|array',
        'master' => 'required|array',
    ];

    public function postSave(Request $request)
    {
        $data = $request->all();
        // Prüfen, ob Dozent bereits einen Stundenplan für das Semester hat
        $timetable = Timetable::where('semester_id', $data['semester_id'])
            ->where('lecturer_id', $data['lecturer_id'])
            ->first();

        if ($timetable) {
            // Stundenplan existiert bereits --> Fehlermeldung
            return redirect()->back()->with('error', 'Es wurde bereits ein Zeitplan für dieses Semester gespeichert.');
        }

        $data = $request->validate([
            'semester_id' => 'required|exists:semesters,id',
            'lecturer_id' => 'required|exists:lecturers,id',
            'teaching_load' => 'required|numeric',
            'max_hours_per_day' => 'required|numeric',
            'use_cn0003' => 'nullable|boolean',
            'use_group_rooms' => 'nullable|boolean',
            'comments' => 'nullable|string',
            'bachelor' => 'nullable|array',
            'master' => 'nullable|array',
        ]);

        // Nur speichern wenn entweder Bachelor oder Master Zeiten vorhanden sind
        if (empty($data['bachelor']) && empty($data['master'])) {
            return redirect()->back()->with('error', 'Bitte gib mindestens einen Zeitraum für Bachelor oder Master an.');
        }

        // Speichern der allgemeinen Informationen
        $timetable = new Timetable();
        $timetable->semester_id = $data['semester_id'];
        $timetable->lecturer_id = $data['lecturer_id'];
        $timetable->teaching_load = $data['teaching_load'];
        $timetable->max_hours_per_day = $data['max_hours_per_day'];
        $timetable->use_cn0003 = $data['use_cn0003'] ?? false;
        $timetable->use_group_rooms = $data['use_group_rooms'] ?? false;
        $timetable->comments = $data['comments'];
        $timetable->bachelor = $data['bachelor'];
        $timetable->master = $data['master'];
        $timetable->save();

        return redirect()->back()->with('success', 'Deine Zeitpräferenzen wurden erfolgreich übermittelt.');
    }

    public function pendingLecturers()
    {
        // Hole das aktuelle Semester
        $currentSemester = Semester::orderBy('created_at', 'desc')->first();

        // Hole alle Benachrichtigungen für das aktuelle Semester, bei denen noch kein Stundenplan eingereicht wurde
        $notifications = Notification::where('semester_id', $currentSemester->id)
            ->where('done', true)
            ->get();

        $pendingLecturers = Lecturer::whereIn('id', function ($query) use ($currentSemester) {
            $query->select('lecturer_id')
            ->from('notifications')
            ->where('semester_id', $currentSemester->id)
                ->where('done', true)
                ->whereNotIn('lecturer_id', function ($subQuery) use ($currentSemester) {
                    $subQuery->select('lecturer_id')
                    ->from('timetables')
                    ->where('semester_id', $currentSemester->id);
                });
        })->get();


        return view('timetables.pending', compact('pendingLecturers', 'currentSemester'));
    }
}
