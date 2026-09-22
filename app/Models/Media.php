<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'file_name',
        'file_path',
        'folder',
        'subfolder',
        'extension',
        'file_size',
        'dimensions',
        'mime_type',
    ];

    protected $appends = [
        'url',
        'name',
        'path',
        'size_formatted',
        'date_formatted',
    ];

    public function getNameAttribute(): string
    {
        return $this->file_name ?? '';
    }

    public function getPathAttribute(): string
    {
        return $this->file_path ?? '';
    }

    /**
     * Get the full asset URL for this media item.
     */
    public function getUrlAttribute(): string
    {
        return asset($this->file_path);
    }

    /**
     * Format bytes to human readable format.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        return round($bytes / pow(1024, $power), 1) . ' ' . $units[$power];
    }

    public function getSizeFormattedAttribute(): string
    {
        return $this->getFormattedSizeAttribute();
    }

    public function getDateFormattedAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('d M Y, h:i A') : '';
    }
}
