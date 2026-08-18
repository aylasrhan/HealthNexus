<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\back\gnr_m_clinics;
use App\Models\back\Question;
use App\Repositories\Questions\IQuestionsRepository;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private IQuestionsRepository $QuestionsRepository;

    public function __construct(IQuestionsRepository $QuestionsRepository)
    {
        $this->QuestionsRepository = $QuestionsRepository;
    }
    public function index()
    {
        abort_unless(auth()->user()->hasSystemRole('admin', 'super_admin', 'reception', 'receptionist', 'secretary'), 403);
        $questions = $this->QuestionsRepository->index();
        return view('back.questions.index', compact('questions'));
    }

    public function show(string $id)
    {
        $allowed = auth()->user()->hasSystemRole('admin', 'super_admin', 'reception', 'receptionist', 'secretary')
            || (auth()->user()->hasSystemRole('doctor') && (int) auth()->user()->doctor?->subgrp === (int) $id);
        abort_unless($allowed, 403);
        $questions = $this->QuestionsRepository->show($id);
        return view('back.questions.show', compact('questions'));
    }

    public function answerTheQ(string $section){
        abort_unless(auth()->user()->hasSystemRole('doctor') && (int) auth()->user()->doctor?->subgrp === (int) $section, 403);
        $questions = $this->QuestionsRepository->answerTheQ($section);
        return view('back.questions.show', compact('questions'));
    }

    public function userQuestions(string $user){
        abort_unless((int) auth()->id() === (int) $user || auth()->user()->hasSystemRole('admin', 'super_admin'), 403);
        $questions = $this->QuestionsRepository->userQuestions($user);
        return view('back.questions.show', compact('questions'));
    }

    public function create()
    {
        $section = gnr_m_clinics::all();
        return view('back.questions.create',compact('section'));
    }

    public function store(Request $request)
    {
        if($request->section !== null && $request->Question !== null){
            try {
                $this->QuestionsRepository->store($request);
                return redirect()->route('questions.show', $request->section)->with('success', ' updated!');

            } catch (\Exception $ex) {
                return redirect()->back()->with(['error' => $ex]);

            }
        }else{
            return redirect()->back()->with(['error' => 'يجب ان تختار القسم و تدخل السؤال']);
        }

    }

    public function edit(string $id)
    {
        $section = gnr_m_clinics::all();
        $qu = Question::findOrFail($id);
        $this->authorize('update', $qu);
        return view('back.questions.edit',compact('section','qu'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, string $id)
    {
        $question = Question::findOrFail($id);
        $this->authorize('update', $question);
        $request->validate([
            'Question' => ['required', 'string', 'max:2000'],
            'answer' => ['nullable', 'string', 'max:4000'],
            'section' => ['required', 'integer', 'exists:gnr_m_clinics,id'],
        ]);
        try {
            $Update = $this->QuestionsRepository->update($request,$id);
            return redirect()->route('questions.show', $request->section)->with('success', ' updated!');

        } catch (\Exception $ex) {

            return redirect()->back()->with(['error' => $ex]);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $question = Question::findOrFail($request->input('input', $request->route('question')));
        $this->authorize('delete', $question);
        try {
            $question->delete();
            return redirect()->back()->with('success', 'تم حذف السؤال بنجاح.');
        } catch (\Exception $ex) {
            return ['result' =>"يوجد خطأ ما",'data' => $ex];

        }
    }
    public function replyToQuestion(Request $request, $id)
    {
        $validated = $request->validate([
            'answer' => ['required', 'string', 'max:5000'],
        ]);

        // تحديث حقل answer في جدول question
        \Illuminate\Support\Facades\DB::table('question')->where('id', $id)->update([
            'answer' => $validated['answer'],
            'updated_at' => now(),
        ]);

        return back()->with('success', 'تم إرسال الرد للمريض بنجاح.');
    }
}
