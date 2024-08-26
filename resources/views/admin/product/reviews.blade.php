@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Reviews')</th>
                                <th>@lang('Rating')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $review)
                            <tr>
                                <td data-label="@lang('Name')">
                                    <div class="user">
                                        <div class="thumb">
                                            <img src="{{ getImage(imagePath()['profile']['user']['path'].'/'. @$review->user->image,null,true) }}" alt="@lang('image')">
                                        </div>
                                        <span class="name"><a href="{{ route('admin.users.detail', $review->user->id) }}">{{ __($review->user->username) }}</a></span>
                                    </div>
                                </td>
                                <td data-label="@lang('Reviews')">
                                    <span>
                                        {{ str_limit($review->review_comment,40) }}
                                        <button type="button" data-review="{{ $review->review_comment }}" class=" icon-btn btn--info btn-sm reviewBtn"><i class="las la-eye"></i></button>
                                    </span>
                                </td>
                                <td data-label="@lang('Rating')">
                                    <span class="name">{{ getAmount($review->stars)}} @lang('stars')</span>
                                </td>
                                
                                <td data-label="@lang('Action')">
                                    <a href="javascript:void(0)" class="icon-btn btn--danger removeBtn" data-toggle="tooltip" title="" data-original-title="@lang('Remove')" data-review_id="{{ $review->id }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty 
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            @if ($reviews->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($reviews) }}
            </div>
            @endif
        </div><!-- card end -->
    </div>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="deleteModalLabel">@lang('Review')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <div class="modal-body">
                <p class="text-muted review-detail"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--dark w-100" data-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="deleteModalLabel">@lang('Remove Review')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            </div>
            <form method="post" action="">
                @csrf
                <div class="modal-body">
                    <p class="text-muted">@lang('Are you sure to delete?')</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--danger">@lang('Delete')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
<a href="{{ route('admin.product.index') }}" class="btn btn-sm btn--primary"><i class="las la-undo"></i> @lang('Back')</a>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"
            $('.reviewBtn').on('click', function() {
                var modal = $('#reviewModal');
                var review = $(this).data('review');
                modal.find('.review-detail').text(review);
                modal.modal('show');
            });
            $('.removeBtn').on('click', function() {
                var modal = $('#deleteModal');
                modal.find('form').attr('action', `{{ route('admin.product.review.remove','') }}/${$(this).data('review_id')}`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush