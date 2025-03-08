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
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class SecurityQuestionController extends Controller
{
    /**
     * Fetch all security questions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Fetch all security questions with only id and question fields
        $questions = SecurityQuestion::select('id', 'question')->get()->take(3);

        // Return the response
        return response()->json([
            'status' => 'success',
            'code' => 200,
            'message' => 'Security questions fetched successfully.',
            'data' => [
                'questions' => $questions,
            ],
            'metadata' => null,
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
     * Store user answers to security questions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        

        // Validate the request using Laravel's built-in validation
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'answers' => 'required|array|size:3',
            'answers.*.question_id' => [
                'required',
                'exists:security_questions,id',
                // Rule::unique('user_security_answers')->where(function ($query) use ($request) {
                //     return $query->where('user_id', $request->user_id);
                // }),
            ],
            'answers.*.answer' => 'required|string|min:1|not_regex:/^\s*$/',
        ], [
            'answers.*.answer.required' => 'The answer must not be empty',
            'answers.size' => 'You must answer exactly 3 security questions.',
            'answers.*.question_id.exists' => 'The question is not in the list.',
            'answers.*.question_id.unique' => 'Each question can only be answered once.',
            'answers.*.answer.not_regex' => 'The answer cannot be empty or consist of only whitespace.',
        ]);

        // Store the answers
        foreach ($request->answers as $answer) {
            UserSecurityAnswer::create([
                'user_id' => $request->user_id,
                'question_id' => $answer['question_id'],
                'answer' => trim($answer['answer']), // Trim whitespace
            ]);
        }

        // Return a success response
        return response()->json([
            'status' => 'success',
            'code' => 201,
            'message' => 'Security answers stored successfully.',
            'data' => null,
            'metadata' => null,
        ], 201);
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
