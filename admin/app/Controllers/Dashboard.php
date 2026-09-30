<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\CareerModel;
use App\Models\GalleryModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $apps    = new ApplicationModel();
        $careers = new CareerModel();
        $gallery = new GalleryModel();
        $db      = db_connect();

        // Last 7 days of applications (zero-filled)
        $rows = $db->query(
            'SELECT DATE(created_at) AS d, COUNT(*) AS c FROM applications
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) GROUP BY DATE(created_at)'
        )->getResultArray();
        $map = array_column($rows, 'c', 'd');

        $labels = $values = [];
        for ($i = 6; $i >= 0; $i--) {
            $day      = date('Y-m-d', strtotime("-{$i} day"));
            $labels[] = date('D, d M', strtotime($day));
            $values[] = (int) ($map[$day] ?? 0);
        }

        // Applications per status
        $statusRows = $db->query('SELECT status, COUNT(*) AS c FROM applications GROUP BY status')->getResultArray();
        $statusMap  = array_column($statusRows, 'c', 'status');
        $statuses   = [];
        foreach (ApplicationModel::STATUSES as $s) {
            $statuses[$s] = (int) ($statusMap[$s] ?? 0);
        }

        $data = [
            'title'        => 'Dashboard',
            'totalGallery' => $gallery->countAllResults(),
            'openJobs'     => $careers->where('status', 1)->countAllResults(),
            'totalApps'    => $apps->countAllResults(),
            'newApps'      => $apps->where('status', 'New')->countAllResults(),
            'chartLabels'  => $labels,
            'chartValues'  => $values,
            'statuses'     => $statuses,
            'recent'       => $apps->filtered()->orderBy('applications.id', 'DESC')->limit(6)->find(),
        ];

        return view('dashboard/index', $data);
    }
}
