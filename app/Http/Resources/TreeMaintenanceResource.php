<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TreeMaintenanceResource extends JsonResource
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
            'tree_id' => $this->tree_id,
            'user_id' => $this->user_id,
            'event_id' => $this->event_id,
            'activity_type' => $this->activity_type,
            'activity_type_name' => $this->activity_type_name,
            'activity_icon' => $this->activity_icon,
            'notes' => $this->notes,
            'images' => $this->images,
            'image_urls' => $this->image_urls,
            'performed_at' => $this->performed_at->format('Y-m-d'),
            'performed_at_formatted' => $this->performed_at_formatted,
            'condition_after' => $this->condition_after,
            'condition_name' => $this->condition_name,
            'condition_color' => $this->condition_color,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            
            // Relationships
            'tree' => [
                'id' => $this->tree->id,
                'species' => $this->tree->species,
                'type' => $this->tree->type,
                'status' => $this->tree->status,
                'location' => [
                    'latitude' => $this->tree->latitude,
                    'longitude' => $this->tree->longitude,
                    'address' => $this->tree->address,
                ]
            ],
            
            'maintainer' => [
                'id' => $this->maintainer->id,
                'name' => $this->maintainer->name,
                'email' => $this->maintainer->email,
            ],
            
            'event' => $this->when($this->event, function() {
                return [
                    'id' => $this->event->id,
                    'title' => $this->event->title,
                    'date' => $this->event->date->format('Y-m-d H:i:s'),
                    'type' => $this->event->type,
                ];
            }),
        ];
    }
}
