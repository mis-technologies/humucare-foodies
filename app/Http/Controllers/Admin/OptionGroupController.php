<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\OptionGroup;
use App\Models\Product;
use Illuminate\Http\Request;

/**
 * Item modifiers: "Choose your size", "Add extras", "Spice level".
 * Groups are reusable — one group can be attached to many dishes.
 */
class OptionGroupController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle    = 'Item Modifiers';
        $emptyMessage = 'No modifier group yet. Create one to offer sizes, extras or add-ons.';

        // `products` is eager-loaded because the view reads the attached ids to
        // pre-tick the "Assign dishes" modal — without it that's one query per group.
        $groups = OptionGroup::withCount('products')->with('allOptions', 'products:id');

        if ($request->search) {
            $groups->where('name', 'LIKE', "%$request->search%");
        }

        $groups   = $groups->orderBy('sort_order')->latest()->paginate(getPaginate());
        $products = Product::orderBy('name')->get(['id', 'name']);

        return view('admin.option_group.index', compact('pageTitle', 'emptyMessage', 'groups', 'products'));
    }

    /** create or update a group */
    public function store(Request $request, $id = 0)
    {
        $request->validate([
            'name'       => 'required|string|max:120',
            'type'       => 'required|in:single,multi',
            'min_select' => 'nullable|integer|min:0|max:20',
            'max_select' => 'nullable|integer|min:1|max:20',
        ]);

        $group = $id ? OptionGroup::findOrFail($id) : new OptionGroup();

        $group->name        = $request->name;
        $group->type        = $request->type;
        $group->is_required = $request->is_required ? 1 : 0;
        // a single-choice group can never take more than one selection
        $group->max_select  = $request->type == 'single' ? 1 : max(1, (int) $request->max_select);
        $group->min_select  = $group->is_required ? max(1, (int) $request->min_select) : (int) $request->min_select;
        $group->sort_order  = (int) $request->sort_order;

        if ($id) {
            $group->status = $request->status ? 1 : 0;
        }

        $group->save();

        $notify[] = ['success', $id ? 'Modifier group updated.' : 'Modifier group created.'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        $group         = OptionGroup::findOrFail($id);
        $group->status = $group->status ? 0 : 1;
        $group->save();

        $notify[] = ['success', 'Status updated.'];
        return back()->withNotify($notify);
    }

    /** add or edit a single choice inside a group */
    public function optionStore(Request $request, $id = 0)
    {
        $request->validate([
            'option_group_id' => 'required|integer|exists:option_groups,id',
            'name'            => 'required|string|max:120',
            'price'           => 'required|numeric|min:0',
        ]);

        $option = $id ? Option::findOrFail($id) : new Option();

        $option->option_group_id = $request->option_group_id;
        $option->name            = $request->name;
        $option->price           = $request->price;
        $option->sort_order      = (int) $request->sort_order;
        $option->status          = $request->status !== null ? ($request->status ? 1 : 0) : 1;
        $option->save();

        $notify[] = ['success', $id ? 'Option updated.' : 'Option added.'];
        return back()->withNotify($notify);
    }

    public function optionDelete($id)
    {
        Option::findOrFail($id)->delete();

        $notify[] = ['success', 'Option removed.'];
        return back()->withNotify($notify);
    }

    /** attach the group to dishes */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'products'   => 'nullable|array',
            'products.*' => 'integer|exists:products,id',
        ]);

        $group = OptionGroup::findOrFail($id);
        $group->products()->sync($request->products ?? []);

        $notify[] = ['success', 'Assigned to ' . count($request->products ?? []) . ' dish(es).'];
        return back()->withNotify($notify);
    }
}
