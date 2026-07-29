@extends('admin.layouts.app')
@section('panel')

<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-info" role="alert">
            <i class="las la-info-circle"></i>
            @lang('This is the clip shown beside "Why you should choose Foodies" on the home page.
            Upload a new one to replace it — it is stored in your S3 bucket, so it survives redeploys.
            Keep it short and muted: it autoplays on loop with no sound.')
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card b-radius--10">
            <div class="card-body">
                <form action="{{ route('admin.homepage.video.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>@lang('New video')</label>
                        <input type="file" name="video" class="form-control" accept="video/mp4,video/webm,video/quicktime" required>
                        <small class="text-muted">
                            @lang('MP4, WebM or MOV. Maximum 50MB. A vertical (portrait) clip suits the layout best.')
                        </small>
                    </div>
                    <button type="submit" class="btn btn--primary">
                        <i class="las la-cloud-upload-alt"></i> @lang('Upload video')
                    </button>
                </form>

                @if ($current)
                <hr>
                <form action="{{ route('admin.homepage.video.reset') }}" method="POST"
                    onsubmit="return confirm('@lang('Remove the uploaded video and go back to the default?')')">
                    @csrf
                    <button type="submit" class="btn btn--danger btn-sm">
                        <i class="las la-undo"></i> @lang('Revert to default video')
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card b-radius--10">
            <div class="card-header">
                <h5 class="card-title mb-0">@lang('Currently showing')</h5>
            </div>
            <div class="card-body text-center">
                <video src="{{ $videoUrl }}" muted loop autoplay playsinline
                    style="max-width:100%;border-radius:10px;max-height:360px"></video>
                <p class="text-muted mt-3 mb-0">
                    <small>
                        @if ($current)
                            @lang('Custom upload'): {{ $current }}
                        @else
                            @lang('Default bundled clip (foodies.mp4)')
                        @endif
                    </small>
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
