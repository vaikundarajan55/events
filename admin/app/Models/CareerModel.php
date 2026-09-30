<?php

namespace App\Models;

use CodeIgniter\Model;

class CareerModel extends Model
{
    protected $table            = 'careers';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['title', 'department', 'location', 'job_type', 'experience', 'description', 'status'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    /** Job list with the number of applicants per job. */
    public function withApplicantCount(): static
    {
        return $this->select(
            'careers.*, (SELECT COUNT(*) FROM applications a WHERE a.career_id = careers.id) AS applicants',
            false
        );
    }
}
