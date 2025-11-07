<?php

namespace App\Http\Controllers;

use App\Models\MatchCondition;
use App\Models\RouteMatch;
use Illuminate\Http\Request;

class MatchConditionController extends Controller
{
    /**
     * Store a new condition for a match
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'match_id' => 'required|exists:matches,id',
            'field' => 'required|string|max:128',
            'operator' => 'required|in:EQUAL,SUP,INF,ELT,IN',
            'value' => 'nullable|string|max:512',
        ]);

        $condition = MatchCondition::create($validated);

        return response()->json([
            'success' => true,
            'condition' => $condition,
            'message' => 'Condition added successfully',
        ]);
    }

    /**
     * Update a condition
     */
    public function update(Request $request, MatchCondition $condition)
    {
        $validated = $request->validate([
            'field' => 'required|string|max:128',
            'operator' => 'required|in:EQUAL,SUP,INF,ELT,IN',
            'value' => 'nullable|string|max:512',
        ]);

        $condition->update($validated);

        return response()->json([
            'success' => true,
            'condition' => $condition,
            'message' => 'Condition updated successfully',
        ]);
    }

    /**
     * Delete a condition
     */
    public function destroy(MatchCondition $condition)
    {
        $condition->delete();

        return response()->json([
            'success' => true,
            'message' => 'Condition deleted successfully',
        ]);
    }
}
