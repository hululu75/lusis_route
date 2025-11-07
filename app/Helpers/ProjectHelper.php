<?php

namespace App\Helpers;

use App\Models\Project;
use Illuminate\Support\Facades\Session;

class ProjectHelper
{
    /**
     * Get current project ID from session
     */
    public static function getCurrentProjectId(): ?int
    {
        return Session::get('current_project_id');
    }

    /**
     * Get current project model
     */
    public static function getCurrentProject(): ?Project
    {
        $projectId = self::getCurrentProjectId();
        return $projectId ? Project::find($projectId) : null;
    }

    /**
     * Check if a project is currently selected
     */
    public static function hasCurrentProject(): bool
    {
        return self::getCurrentProjectId() !== null;
    }

    /**
     * Scope query to current project if one is selected
     */
    public static function scopeToCurrentProject($query, string $projectIdColumn = 'project_id')
    {
        $projectId = self::getCurrentProjectId();
        
        if ($projectId) {
            return $query->where($projectIdColumn, $projectId);
        }

        return $query;
    }
}
