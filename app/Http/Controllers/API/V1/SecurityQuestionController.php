<?php

namespace App\Http\Controllers\API\V1;

use App\Models\SecurityQuestion;
use App\Http\Requests\StoreSecurityQuestionRequest;
use App\Http\Requests\UpdateSecurityQuestionRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use App\Models\User;
use App\Models\UserSecurityAnswer;
use Illuminate\Support\Facades\Validator;

class SecurityQuestionController extends Controller
{
    /**
     * Fetch all security questions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $questions = SecurityQuestion::all();

        return response()->json([
            'questions' => $questions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(StoreSecurityQuestionRequest $request)
    // {
    //     //
    // }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'answers' => 'nullable|array|max:3', // Allow skipping (nullable)
            'answers.*.question_id' => 'required_with:answers|exists:security_questions,id',
            'answers.*.answer' => 'required_with:answers|string',
        ]);

        $user = User::findOrFail($request->user_id);

        // Delete existing answers (if any)
        $user->securityAnswers()->delete();

        // Save new answers (if provided)
        if ($request->answers) {
            foreach ($request->answers as $answer) {
                $user->securityAnswers()->create([
                    'question_id' => $answer['question_id'],
                    'answer' => $answer['answer'],
                ]);
            }
        }

        return response()->json([
            'message' => $request->answers ? 'Security answers saved successfully' : 'Security answers skipped',
        ]);
    }

    /**
     * Display the specified resource.
     */
    // public function show(SecurityQuestion $securityQuestion)
    // {
    //     //
    // }

    public function show(Request $request)
    {
        $user = $request->user();

        $answers = $user->securityAnswers()->with('question')->get();

        return response()->json([
            'answers' => $answers,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SecurityQuestion $securityQuestion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSecurityQuestionRequest $request, SecurityQuestion $securityQuestion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SecurityQuestion $securityQuestion)
    {
        //
    }

    public function getUserSecurityQuestions($userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Fetch the user's security questions and answers
        $questions = $user->securityAnswers()->with('question')->get();

        if ($questions->isEmpty()) {
            return response()->json(['message' => 'No security questions answered. Please contact the main office.'], 404);
        }

        return response()->json([
            'questions' => $questions->map(function ($answer) {
                return [
                    'question_id' => $answer->question_id,
                    'question' => $answer->question->question,
                ];
            }),
        ]);
    }
}
