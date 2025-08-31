<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('student.index', [
            'students' => Student::Paginate(5)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('student.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StorestudentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorestudentRequest $request)
    {
        $validated = $request->validated();
        
        // Generate unique student ID and library card number
        $validated['student_id'] = $this->generateStudentId();
        $validated['library_card_number'] = $this->generateLibraryCardNumber();
        $validated['enrollment_date'] = now()->toDateString();
        $validated['status'] = 'active';

        Student::create($validated);

        return redirect()->route('students')
            ->with('success', 'Student added successfully!');
    }

    /**
     * Generate a unique student ID
     */
    private function generateStudentId()
    {
        $year = date('Y');
        $lastStudent = Student::whereYear('created_at', $year)
                             ->whereNotNull('student_id')
                             ->where('student_id', '!=', '')
                             ->orderBy('id', 'desc')
                             ->first();
        
        if ($lastStudent && $lastStudent->student_id) {
            // Extract number from existing student ID (e.g., STU2025001 -> 001)
            $lastNumber = (int) substr($lastStudent->student_id, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return 'STU' . $year . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a unique library card number
     */
    private function generateLibraryCardNumber()
    {
        $prefix = 'LIB';
        $lastCard = Student::whereNotNull('library_card_number')
                           ->where('library_card_number', '!=', '')
                           ->orderBy('id', 'desc')
                           ->first();
        
        if ($lastCard && $lastCard->library_card_number) {
            // Extract number from existing card (e.g., LIB000001 -> 1)
            $lastNumber = (int) substr($lastCard->library_card_number, 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $student = Student::find($id);
        if (!$student) {
            abort(404, 'Student not found');
        }

        // Check if this is an AJAX request
        if (request()->ajax()) {
            return response()->json($student);
        }

        // For regular requests, return the view
        return view('student.show', compact('student'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(student $student)
    {
        return view('student.edit', [
            'student' => $student
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatestudentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateStudentRequest $request, $id)
    {
        $student = Student::find($id);
        $student->name = $request->input('name');
        $student->address = $request->input('address');
        $student->gender = $request->input('gender');
        $student->class = $request->input('class');
        $student->age = $request->input('age');
        $student->phone = $request->input('phone');
        $student->email = $request->input('email');
        $student->save();

        return redirect()->route('students');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Student::find($id)->delete();
        return redirect()->route('students');
    }
}
