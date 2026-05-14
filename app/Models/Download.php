<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    
    protected $fillable = [
        'video_id',
        'title',
        'thumbnail',
        'duration',
        'format_id',
        'quality',
        'extension',
        'status',
        'progress',
        'file_path',
        'file_size',
        'error_message',
    ];

    
    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'progress' => 'integer',
            'file_size' => 'integer',
        ];
    }

    
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->duration) {
            return '00:00';
        }

        $hours = floor($this->duration / 3600);
        $minutes = floor(($this->duration % 3600) / 60);
        $seconds = $this->duration % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }
}
