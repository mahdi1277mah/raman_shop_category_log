<?php

namespace App\Http\Livewire\Admin\Childcategory;

use App\Models\ChildCategory;
use App\Models\Log;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Trashed extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $img;
    public $search;

    protected $queryString = ['search'];

    public $readyToLoad = false;

    public function loadCategory()
    {
        $this->readyToLoad = true;
    }


    public function deleteCategory($id)
    {
        $category = ChildCategory::find($id);
        $category->delete();
        $this->emit('toast', 'success', ' زیر دسته با موفقیت حذف شد.');
    }

    public function trashedCategory($id)
    {
        $category = ChildCategory::withTrashed()->where('id', $id)->first();
        $category->restore();
        Log::create([
            'user_id' => auth()->user()->id,
            'url' => 'بازیابی دسته فرزند' .'-'. $category->title,
            'actionType' => 'بازیابی'
        ]);
        $this->emit('toast', 'success', ' دسته فرزند با موفقیت بازیابی شد.');
    }

    public function render()
    {

        $categories = $this->readyToLoad ? DB::table('child_categories')
            ->whereNotNull('deleted_at')->
            latest()->paginate(15) : [];

        return view('livewire.admin.childcategory.trashed',compact('categories'));
    }
}
