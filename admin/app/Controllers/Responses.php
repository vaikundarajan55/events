<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\CareerModel;

class Responses extends BaseController
{
    private const PER_PAGE = 10;

    private ApplicationModel $model;

    public function __construct()
    {
        $this->model = new ApplicationModel();
    }

    public function index()
    {
        [$q, $job, $status] = $this->filters();

        $items = $this->model->filtered($q, $job, $status)
                             ->orderBy('applications.id', 'DESC')
                             ->paginate(self::PER_PAGE);
        $pager = $this->model->pager;

        return view('responses/index', [
            'title'    => 'Career responses',
            'items'    => $items,
            'pager'    => $pager,
            'total'    => $pager->getTotal(),
            'from'     => $pager->getTotal() ? ($pager->getCurrentPage() - 1) * self::PER_PAGE + 1 : 0,
            'to'       => min($pager->getCurrentPage() * self::PER_PAGE, $pager->getTotal()),
            'q'        => $q,
            'job'      => $job,
            'status'   => $status,
            'jobs'     => (new CareerModel())->select('id, title')->orderBy('title')->findAll(),
            'statuses' => ApplicationModel::STATUSES,
        ]);
    }

    public function show(int $id)
    {
        $row = $this->model->filtered()->where('applications.id', $id)->first();
        if (! $row) {
            return redirect()->to('/responses')->with('error', 'Application not found.');
        }
        // First view of a new application marks it as reviewed
        if ($row['status'] === 'New') {
            $this->model->update($id, ['status' => 'Reviewed']);
            $row['status'] = 'Reviewed';
        }
        return view('responses/view', [
            'title'    => 'Application',
            'a'        => $row,
            'statuses' => ApplicationModel::STATUSES,
        ]);
    }

    public function status(int $id)
    {
        $status = (string) $this->request->getPost('status');
        if ($this->model->find($id) && in_array($status, ApplicationModel::STATUSES, true)) {
            $this->model->update($id, ['status' => $status]);
            return redirect()->back()->with('success', "Status set to {$status}.");
        }
        return redirect()->back()->with('error', 'Could not update status.');
    }

    public function resume(int $id)
    {
        $row  = $this->model->find($id);
        $file = $row ? basename((string) $row['resume']) : '';
        $path = config('Site')->uploadPath . 'resumes' . DIRECTORY_SEPARATOR . $file;

        if ($file === '' || ! is_file($path)) {
            return redirect()->back()->with('error', 'Resume file not found.');
        }
        $ext  = pathinfo($file, PATHINFO_EXTENSION);
        $name = preg_replace('/[^A-Za-z0-9_\-]/', '_', $row['name']) . '_resume.' . $ext;

        return $this->response->download($path, null)->setFileName($name);
    }

    public function delete(int $id)
    {
        $row = $this->model->find($id);
        if ($row) {
            $path = config('Site')->uploadPath . 'resumes' . DIRECTORY_SEPARATOR . basename((string) $row['resume']);
            if (is_file($path)) {
                @unlink($path);
            }
            $this->model->delete($id);
        }
        return redirect()->to('/responses')->with('success', 'Application deleted.');
    }

    /** CSV export of the current filter. */
    public function export()
    {
        [$q, $job, $status] = $this->filters();
        $rows = $this->model->filtered($q, $job, $status)->orderBy('applications.id', 'DESC')->findAll();

        $fh = fopen('php://temp', 'r+');
        fwrite($fh, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
        fputcsv($fh, ['ID', 'Job', 'Name', 'Email', 'Phone', 'Status', 'Message', 'Applied on']);
        foreach ($rows as $r) {
            fputcsv($fh, [$r['id'], $r['job_name'], $r['name'], $r['email'], $r['phone'], $r['status'], $r['message'], $r['created_at']]);
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="applications-' . date('Ymd-His') . '.csv"')
            ->setBody($csv);
    }

    private function filters(): array
    {
        return [
            trim((string) $this->request->getGet('q')),
            (int) $this->request->getGet('job'),
            (string) $this->request->getGet('status'),
        ];
    }
}
