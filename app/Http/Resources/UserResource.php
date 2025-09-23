<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'avatar' => $this->avatar ?? $this->generateAvatar(),
            'created_at' => isset($this->created_at) ? $this->created_at->toISOString() : null,
            'updated_at' => isset($this->updated_at) ? $this->updated_at->toISOString() : null,
            'stats' => isset($this->stats) ? [
                'treesPlanted' => $this->stats->trees_planted ?? 0,
                'eventsAttended' => $this->stats->events_attended ?? 0,
                'projectsJoined' => $this->stats->projects_joined ?? 0,
                'impactScore' => $this->stats->impact_score ?? 0,
            ] : null,
        ];
    }

    /**
     * Generate avatar URL based on user name
     */
    private function generateAvatar(): string
    {
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&color=16a34a&background=dcfce7";
    }
}