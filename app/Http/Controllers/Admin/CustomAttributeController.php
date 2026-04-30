<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomAttributeDefinition;
use Illuminate\Http\Request;

class CustomAttributeController extends Controller
{
    /**
     * Show all defined custom attributes.
     * This is the admin settings page.
     */
    public function index()
    {
        $attributes = CustomAttributeDefinition::orderBy('sort_order')->get();
        return view('admin.custom-attributes.index', compact('attributes'));
    }

    /**
     * Save a new custom attribute definition.
     * Called when admin submits the "Add New Attribute" form.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|unique:custom_attribute_definitions,name',
            'label'       => 'required|string',
            'type'        => 'required|in:text,number,date,select,boolean',
            'options'     => 'nullable|string', // comma-separated e.g. "IT, Finance, HR"
            'is_required' => 'nullable|boolean',
            'sort_order'  => 'nullable|integer',
        ]);

        // Only process options if type is 'select'
        $options = null;
        if ($request->type === 'select' && $request->options) {
            $options = array_map('trim', explode(',', $request->options));
        }

        CustomAttributeDefinition::create([
            'name'        => $request->name,
            'label'       => $request->label,
            'type'        => $request->type,
            'options'     => $options,
            'is_required' => $request->boolean('is_required'),
            'sort_order'  => $request->sort_order ?? 0,
            'is_active'   => true,
        ]);

        return back()->with('success', 'Custom attribute "' . $request->label . '" created.');
    }

    /**
     * Toggle a field active/inactive without deleting it.
     * Inactive fields won't show on member forms.
     */
    public function toggle(CustomAttributeDefinition $customAttribute)
    {
        $customAttribute->update([
            'is_active' => !$customAttribute->is_active,
        ]);

        $status = $customAttribute->is_active ? 'activated' : 'deactivated';
        return back()->with('success', 'Attribute "' . $customAttribute->label . '" ' . $status . '.');
    }

    /**
     * Permanently delete a field and all member values for it.
     */
    public function destroy(CustomAttributeDefinition $customAttribute)
    {
        $customAttribute->delete();
        return back()->with('success', 'Attribute deleted.');
    }
}