<?php

namespace App\Controllers;

use App\Models\GalleryModel;

class Gallery extends BaseController
{
    private const PER_PAGE   = 8;
    private const MAX_KB     = 4096;
    private const ALLOWED    = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    private const ALLOWED_MIME = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    private GalleryModel $model;
    private string $dir;

    public function __construct()
    {
        $this->model = new GalleryModel();
        $this->dir   = config('Site')->uploadPath . 'gallery' . DIRECTORY_SEPARATOR;
    }

    public function index()
    {
        $q      = trim((string) $this->request->getGet('q'));
        $status = $this->request->getGet('status');

        if ($q !== '') {
            $this->model->like('title', $q);
        }
        if ($status === '0' || $status === '1') {
            $this->model->where('status', (int) $status);
        }

        $items = $this->model->orderBy('id', 'DESC')->paginate(self::PER_PAGE);
        $pager = $this->model->pager;

        return view('gallery/index', [
            'title'  => 'Gallery',
            'items'  => $items,
            'pager'  => $pager,
            'total'  => $pager->getTotal(),
            'from'   => $pager->getTotal() ? ($pager->getCurrentPage() - 1) * self::PER_PAGE + 1 : 0,
            'to'     => min($pager->getCurrentPage() * self::PER_PAGE, $pager->getTotal()),
            'q'      => $q,
            'status' => $status,
            'siteUrl' => config('Site')->siteUrl,
        ]);
    }

    public function create()
    {
        return view('gallery/form', ['title' => 'Add images', 'item' => null]);
    }

    public function store()
    {
        $title  = trim((string) $this->request->getPost('title'));
        $status = $this->request->getPost('status') === '0' ? 0 : 1;
        $files  = $this->request->getFileMultiple('images') ?: [];

        if (! $files || ! $files[0]->isValid()) {
            return redirect()->back()->withInput()->with('error', 'Choose at least one image to upload.');
        }
        if (mb_strlen($title) > 150) {
            return redirect()->back()->withInput()->with('error', 'Title must be 150 characters or fewer.');
        }

        $this->ensureDirs();
        $saved = 0;
        $errors = [];

        foreach ($files as $file) {
            $err = $this->checkImage($file);
            if ($err) {
                $errors[] = $file->getClientName() . ': ' . $err;
                continue;
            }
            [$name, $thumb] = $this->saveImage($file);

            $label = $title !== '' ? $title : pathinfo($file->getClientName(), PATHINFO_FILENAME);
            $this->model->insert([
                'title'  => mb_substr($label, 0, 150),
                'image'  => $name,
                'thumb'  => $thumb,
                'status' => $status,
            ]);
            $saved++;
        }

        if ($saved === 0) {
            return redirect()->back()->withInput()->with('error', implode(' | ', $errors));
        }

        $msg = "{$saved} image(s) uploaded.";
        if ($errors) {
            $msg .= ' Skipped: ' . implode(' | ', $errors);
        }
        return redirect()->to('/gallery')->with('success', $msg);
    }

    public function edit(int $id)
    {
        $item = $this->model->find($id);
        if (! $item) {
            return redirect()->to('/gallery')->with('error', 'Image not found.');
        }
        return view('gallery/form', [
            'title'   => 'Edit image',
            'item'    => $item,
            'siteUrl' => config('Site')->siteUrl,
        ]);
    }

    public function update(int $id)
    {
        $item = $this->model->find($id);
        if (! $item) {
            return redirect()->to('/gallery')->with('error', 'Image not found.');
        }

        $title = trim((string) $this->request->getPost('title'));
        if ($title === '' || mb_strlen($title) > 150) {
            return redirect()->back()->withInput()->with('error', 'Title is required (max 150 characters).');
        }

        $data = [
            'title'  => $title,
            'status' => $this->request->getPost('status') === '0' ? 0 : 1,
        ];

        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            $err = $this->checkImage($file);
            if ($err) {
                return redirect()->back()->withInput()->with('error', $err);
            }
            $this->ensureDirs();
            [$name, $thumb] = $this->saveImage($file);
            $this->removeFiles($item);
            $data['image'] = $name;
            $data['thumb'] = $thumb;
        }

        $this->model->update($id, $data);
        return redirect()->to('/gallery')->with('success', 'Image updated.');
    }

    public function toggle(int $id)
    {
        $item = $this->model->find($id);
        if ($item) {
            $this->model->update($id, ['status' => $item['status'] ? 0 : 1]);
        }
        return redirect()->back()->with('success', 'Visibility changed.');
    }

    public function delete(int $id)
    {
        $item = $this->model->find($id);
        if ($item) {
            $this->removeFiles($item);
            $this->model->delete($id);
        }
        return redirect()->to('/gallery')->with('success', 'Image deleted.');
    }

    // ------------------------------------------------------------------

    private function ensureDirs(): void
    {
        foreach ([$this->dir, $this->dir . 'thumbs' . DIRECTORY_SEPARATOR] as $d) {
            if (! is_dir($d)) {
                mkdir($d, 0755, true);
            }
        }
    }

    private function checkImage($file): ?string
    {
        if (! $file->isValid()) {
            return $file->getErrorString();
        }
        if ($file->getSizeByUnit('kb') > self::MAX_KB) {
            return 'larger than 4 MB';
        }
        if (! in_array(strtolower($file->getExtension()), self::ALLOWED, true)
            || ! in_array($file->getMimeType(), self::ALLOWED_MIME, true)) {
            return 'only JPG, PNG, WEBP or GIF allowed';
        }
        return null;
    }

    /** @return array{0:string,1:?string} */
    private function saveImage($file): array
    {
        $name = $file->getRandomName();
        $file->move($this->dir, $name);

        $thumb = null;
        try {
            $thumbName = 't_' . $name;
            \Config\Services::image()
                ->withFile($this->dir . $name)
                ->fit(640, 480, 'center')
                ->save($this->dir . 'thumbs' . DIRECTORY_SEPARATOR . $thumbName, 82);
            $thumb = $thumbName;
        } catch (\Throwable $e) {
            log_message('warning', 'Thumbnail failed: ' . $e->getMessage());
        }
        return [$name, $thumb];
    }

    private function removeFiles(array $item): void
    {
        @unlink($this->dir . $item['image']);
        if (! empty($item['thumb'])) {
            @unlink($this->dir . 'thumbs' . DIRECTORY_SEPARATOR . $item['thumb']);
        }
    }
}
