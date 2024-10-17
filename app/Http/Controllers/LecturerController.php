<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lecturer;
use App\Models\Semester;
use App\Models\Timetable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LecturerController extends Controller
{
    protected $className = 'App\Models\Lecturer';
    protected $entityName = 'lecturers';
    protected $fields = ['firstname', 'lastname', 'email', 'phone', 'active'];
    protected $validation = [
        'firstname' => 'required',
        'lastname' => 'required',
        'email' => 'required|email',
        'phone' => 'required',
        'active' => 'boolean'
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

    public function getImportForm()
    {
        return view('lecturers.import');
    }

    public function postImportCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = fopen($request->file('csv_file'), 'r');

        $header = fgetcsv($file);

        // check if the header is valid
        $expectedHeader = ['firstname', 'lastname', 'email', 'phone'];
        if ($header !== $expectedHeader) {
            return redirect()->back()->with('error', 'Das Format der CSV-Datei ist ungültig. Die Datei muss folgende Spalten enthalten: firstname, lastname, email, phone');
        }

        $errors = [];
        $successCount = 0;

        while (($row = fgetcsv($file, 1000, ',')) !== FALSE) {
            $data = [
                'firstname' => $row[0],
                'lastname' => $row[1],
                'email' => $row[2],
                'phone' => $row[3],
                'active' => isset($row[4]) && $row[4] !== '' ? (int)$row[4] : 1 // Set active to 1 if not specified
            ];

            // Validation of the lecturer data
            $validator = Validator::make($data, [
                'firstname' => 'required|string|max:255',
                'lastname' => 'required|string|max:255',
                'email' => 'required|email|unique:lecturers,email',
                'phone' => 'nullable|string|max:20',
                'active' => 'sometimes|boolean',
            ]);

            if ($validator->fails()) {
                $errors[] = "Fehler in Zeile {$row[0]}: " . implode(", ", $validator->errors()->all());
            } else {
                Lecturer::updateOrCreate(['email' => $data['email']], $data);
                $successCount++;
            }
        }

        fclose($file);

        if (count($errors) === 0) {
            return redirect('lecturers')->with('success', "$successCount Dozenten erfolgreich importiert.");
        }

        return redirect()->back()->with('error', implode("\n", $errors))->with('success', "$successCount Dozenten erfolgreich importiert.");
    }
}