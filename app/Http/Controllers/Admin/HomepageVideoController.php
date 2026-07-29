<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frontend;
use Illuminate\Http\Request;

/**
 * The clip beside "Why you should choose Foodies" on the home page.
 *
 * It used to be a hardcoded /foodies.mp4 in the Blade file, so changing it meant
 * a redeploy. Uploads go through uploadFile(), which writes to S3 when
 * configured — necessary on Docker, where anything saved to the container
 * filesystem is lost on the next deploy.
 */
class HomepageVideoController extends Controller
{
    /** Where the current filename lives, reusing the frontend-content table. */
    protected function record()
    {
        return Frontend::where('data_keys', 'why_choose.content')->first();
    }

    public function index()
    {
        $pageTitle = 'Home Page Video';
        $current   = optional($this->record())->data_values->video ?? null;
        $videoUrl  = homepageVideoUrl();

        return view('admin.frontend.homepage_video', compact('pageTitle', 'current', 'videoUrl'));
    }

    public function update(Request $request)
    {
        $request->validate([
            // mimetypes rather than extensions: the browser reports the real
            // container type, so a renamed .exe cannot slip through
            'video' => 'required|file|max:51200|mimetypes:video/mp4,video/webm,video/quicktime',
        ], [
            'video.max'       => 'The video may not be larger than 50MB.',
            'video.mimetypes' => 'Upload an MP4, WebM or MOV file.',
        ]);

        $frontend = $this->record();

        if (!$frontend) {
            $frontend             = new Frontend();
            $frontend->data_keys  = 'why_choose.content';
            $frontend->tempname   = activeTemplate();
        }

        $values = (array) ($frontend->data_values ?? []);
        $old    = $values['video'] ?? null;

        try {
            // uploadFile() removes $old for us, on whichever disk is in use
            $values['video'] = uploadFile($request->file('video'), 'assets/frontend/video', null, $old);
        } catch (\Throwable $e) {
            $notify[] = ['error', 'Video could not be uploaded: ' . $e->getMessage()];
            return back()->withNotify($notify);
        }

        $frontend->data_values = $values;
        $frontend->save();

        $notify[] = ['success', 'Home page video has been updated.'];
        return back()->withNotify($notify);
    }

    /** Revert to the bundled clip. */
    public function reset()
    {
        $frontend = $this->record();

        if ($frontend) {
            $values = (array) ($frontend->data_values ?? []);

            if (!empty($values['video'])) {
                removeFile('assets/frontend/video/' . $values['video']);
            }

            unset($values['video']);
            $frontend->data_values = $values;
            $frontend->save();
        }

        $notify[] = ['success', 'Reverted to the default video.'];
        return back()->withNotify($notify);
    }
}
