<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationModel extends Model
{
    public const STATUSES = ['New', 'Reviewed', 'Shortlisted', 'Rejected'];

    protected $table            = 'applications';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['career_id', 'job_title', 'name', 'email', 'phone', 'message', 'resume', 'status'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /** Applies the search / job / status filters used on the responses page. */
    public function filtered(string $q = '', int $jobId = 0, string $status = ''): static
    {
        $this->select('applications.*, COALESCE(careers.title, applications.job_title) AS job_name')
             ->join('careers', 'careers.id = applications.career_id', 'left');

        if ($q !== '') {
            $this->groupStart()
                    ->like('applications.name', $q)
                    ->orLike('applications.email', $q)
                    ->orLike('applications.phone', $q)
                 ->groupEnd();
        }
        if ($jobId > 0) {
            $this->where('applications.career_id', $jobId);
        }
        if (in_array($status, self::STATUSES, true)) {
            $this->where('applications.status', $status);
        }

        return $this;
    }
}
