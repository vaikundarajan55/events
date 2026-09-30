<?php

namespace App\Controllers;

use App\Models\CareerModel;

class Careers extends BaseController
{
    private const PER_PAGE = 8;
    private const TYPES    = ['Full Time', 'Part Time', 'Contract', 'Internship'];

    private CareerModel $model;

    public function __construct()
    {
        $this->model = new CareerModel();
    }

    public function index()
    {
        $q      = trim((string) $this->request->getGet('q'));
        $status = $this->request->getGet('status');

        $this->model->withApplicantCount();
        if ($q !== '') {
            $this->model->groupStart()
                ->like('title', $q)->orLike('department', $q)->orLike('location', $q)
                ->groupEnd();
        }
        if ($status === '0' || $status === '1') {
            $this->model->where('careers.status', (int) $status);
        }

        $items = $this->model->orderBy('careers.id', 'DESC')->paginate(self::PER_PAGE);
        $pager = $this->model->pager;

        return view('careers/index', [
            'title'  => 'Careers',
            'items'  => $items,
            'pager'  => $pager,
            'total'  => $pager->getTotal(),
            'from'   => $pager->getTotal() ? ($pager->getCurrentPage() - 1) * self::PER_PAGE + 1 : 0,
            'to'     => min($pager->getCurrentPage() * self::PER_PAGE, $pager->getTotal()),
            'q'      => $q,
            'status' => $status,
        ]);
    }

    public function create()
    {
        return view('careers/form', ['title' => 'Post a job', 'job' => null, 'types' => self::TYPES]);
    }

    public function store()
    {
        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $this->model->insert($this->payload());
        return redirect()->to('/careers')->with('success', 'Job posted.');
    }

    public function edit(int $id)
    {
        $job = $this->model->find($id);
        if (! $job) {
            return redirect()->to('/careers')->with('error', 'Job not found.');
        }
        return view('careers/form', ['title' => 'Edit job', 'job' => $job, 'types' => self::TYPES]);
    }

    public function update(int $id)
    {
        if (! $this->model->find($id)) {
            return redirect()->to('/careers')->with('error', 'Job not found.');
        }
        if (! $this->validate($this->rules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $this->model->update($id, $this->payload());
        return redirect()->to('/careers')->with('success', 'Job updated.');
    }

    public function toggle(int $id)
    {
        $job = $this->model->find($id);
        if ($job) {
            $this->model->update($id, ['status' => $job['status'] ? 0 : 1]);
        }
        return redirect()->back()->with('success', 'Job status changed.');
    }

    public function delete(int $id)
    {
        $this->model->delete($id);
        return redirect()->to('/careers')->with('success', 'Job deleted. Existing applications were kept.');
    }

    private function rules(): array
    {
        return [
            'title'       => 'required|min_length[3]|max_length[150]',
            'department'  => 'required|max_length[100]',
            'location'    => 'required|max_length[100]',
            'job_type'    => 'required|in_list[' . implode(',', self::TYPES) . ']',
            'experience'  => 'permit_empty|max_length[50]',
            'description' => 'required|min_length[20]',
        ];
    }

    private function payload(): array
    {
        return [
            'title'       => trim((string) $this->request->getPost('title')),
            'department'  => trim((string) $this->request->getPost('department')),
            'location'    => trim((string) $this->request->getPost('location')),
            'job_type'    => $this->request->getPost('job_type'),
            'experience'  => trim((string) $this->request->getPost('experience')),
            'description' => trim((string) $this->request->getPost('description')),
            'status'      => $this->request->getPost('status') === '0' ? 0 : 1,
        ];
    }
}
