<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Lecturer;
use App\Models\Semester;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\CustomEmail;
use Illuminate\Support\Facades\URL;

class NotificationController extends Controller
{
    protected $className = 'App\Models\Notification';
    protected $entityName = 'notifications';
    protected $fields = ['done', 'kind'];
    protected $validation = [
        'done' => 'required|boolean',
        'kind' => 'required'
    ];

    public function markOverdue()
    {
        $currentSemester = Semester::orderBy('created_at', 'desc')->first();

        // Hole alle Dozenten, die noch keinen Stundenplan für das aktuelle Semester eingereicht haben
        $lecturers = Lecturer::whereDoesntHave('timetables', function ($query) use ($currentSemester) {
            $query->where('semester_id', $currentSemester->id);
        })->get();

        // Prüfe, wann die letzte Benachrichtigung für das aktuelle Semester versendet wurde
        $lastNotification = Notification::where('semester_id', $currentSemester->id)
            ->orderBy('created_at', 'desc')
            ->first();

        // Hole die Anzahl der Tage, die seit der letzten Benachrichtigung vergangen sind
        $daysSinceLastNotification = $lastNotification ? Carbon::now()->diffInDays($lastNotification->created_at) : 0;

        // Wenn seit der letzten Benachrichtigung mehr als 10 Tage vergangen sind, sende eine neue Benachrichtigung
        if ($daysSinceLastNotification == 7) {
            // Erstelle eine neue Benachrichtigung für jeden Dozenten
            foreach ($lecturers as $lecturer) {
                Notification::create([
                    'lecturer_id' => $lecturer->id,
                    'semester_id' => $currentSemester->id,
                    'done' => false,
                    'kind' => 2
                ]);
            }
        }
        elseif($daysSinceLastNotification == 10) {
            // Erstelle eine neue Benachrichtigung für jeden Dozenten
            foreach ($lecturers as $lecturer) {
                Notification::create([
                    'lecturer_id' => $lecturer->id,
                    'semester_id' => $currentSemester->id,
                    'done' => false,
                    'kind' => 2
                ]);
            }
        }
        elseif($daysSinceLastNotification == 14) {
            // Erstelle eine neue Benachrichtigung für jeden Dozenten
            foreach ($lecturers as $lecturer) {
                Notification::create([
                    'lecturer_id' => $lecturer->id,
                    'semester_id' => $currentSemester->id,
                    'done' => false,
                    'kind' => 3
                ]);
            }
        }
        elseif($daysSinceLastNotification == 30) {
            // Erstelle eine neue Benachrichtigung für jeden Dozenten
            foreach ($lecturers as $lecturer) {
                Notification::create([
                    'lecturer_id' => $lecturer->id,
                    'semester_id' => $currentSemester->id,
                    'done' => false,
                    'kind' => 3
                ]);
            }
        }
    }

    // Start process of sending notifications to lecturers - limit to 10 notifications
    public function sendNotification()
    {
        // get notifications wit done is false
        $notifications = Notification::where('done', false)->limit(5)->get();

        // loop through all notifications
        foreach ($notifications as $notification) {
            // get lecturer
            /** @var \App\Models\Lecturer $lecturer */
            $lecturer = Lecturer::find($notification->lecturer_id);

            // get semester
            $semester = Semester::find($notification->semester_id);

            // send notification to lecturer with email template
            // Generate signed URL and email it to the user
            $timetableUrl = URL::temporarySignedRoute(
                'lecturer.timetable',
                now()->addMonths(3),
                [
                    'lecturer' => $lecturer->id,
                    'semester' => $semester->id
                ]
            );

            // Benachrichtigung an Dozenten mit Aufforderung zur Eingabe seines Stundenplans und Zeitkontingents für das Semester
            switch ($notification->kind) {
                case 1:
                    $lecturer->notify(new CustomEmail(
                        'Dein Zeitplan für das Semester "' . $semester->name . '"',
                        'Zeitmanagement - HS OS',
                        'Hallo ' . $lecturer->firstname . ' ' . $lecturer->lastname . ',',
                        'bitte gib deinen Zeitplan für das ' . $semester->name . ' ein, um die Lehrplanung zu vereinfachen. Klicke dazu auf den folgenden Button:',
                        $timetableUrl,
                        'Zur Eingabe des Zeitplans'
                    ));
                    break;
                case 2:
                    $lecturer->notify(new CustomEmail(
                        'Erinnerung: Dein Stundenplan für das Semester "' . $semester->name . '"',
                        'Zeitmanagement - HS OS',
                        'Hallo ' . $lecturer->firstname . ' ' . $lecturer->lastname . ',',
                        'bitte gib deinen Zeitplan für das ' . $semester->name . ' ein, um die Lehrplanung zu vereinfachen. Klicke dazu auf den folgenden Button:',
                        $timetableUrl,
                        'Zur Eingabe des Zeitplans'
                    ));
                case 3:
                    $lecturer->notify(new CustomEmail(
                        'WICHTIG: Dein Zeitplan für das Semester "' . $semester->name . '"',
                        'Zeitmanagement - HS OS',
                        'Hallo ' . $lecturer->firstname . ' ' . $lecturer->lastname . ',',
                        'bitte gib uns dringend (!) deinen Zeitplan für das ' . $semester->name . ' weiter, um die Lehrplanung zu ermöglichen. Klicke dazu auf den folgenden Button:',
                        $timetableUrl,
                        'Zur Eingabe des Zeitplans'
                    ));
                    break;
            }

            // update notification status
            $notification->done = true;
            $notification->save();
        }

        // return success message
        return redirect('semesters/index')->with('success', 'Benachrichtigungen wurden erfolgreich versendet');
    }

    public function overview()
    {
        // Hole das aktuelle Semester aus der Datenbank (zuletzt erstelltes Semester)
        $currentSemester = Semester::orderBy('created_at', 'desc')->first();

        // Zähle die eingereichten Stundenpläne für das aktuelle Semester
        $submittedTimetables = Timetable::where('semester_id', $currentSemester->id)
            ->count();


        $notifications = Notification::where('semester_id', $currentSemester->id)
            ->where('done', true)
            ->get();

        // Zähle die ausstehenden Antworten (Benachrichtigungen ohne eingereichten Stundenplan)
        $pendingResponses = Lecturer::whereIn('id', function ($query) use ($currentSemester) {
            $query->select('lecturer_id')
            ->from('notifications')
            ->where('semester_id', $currentSemester->id)
            ->where('done', true)
            ->whereNotIn('lecturer_id', function ($subQuery) use ($currentSemester) {
                $subQuery->select('lecturer_id')
                ->from('timetables')
                    ->where('semester_id', $currentSemester->id);
            });
        })->count();

        // $pendingResponses = 2;

        // Zähle die ausstehenden Emails
        $pendingEmails = Notification::where('semester_id', $currentSemester->id)
            ->where('done', false)
            ->count();

        return view('notifications.index', compact('submittedTimetables', 'pendingResponses', 'pendingEmails', 'currentSemester'));
    }
}
