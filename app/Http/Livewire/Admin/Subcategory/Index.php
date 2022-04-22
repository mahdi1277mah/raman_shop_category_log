<?php

namespace App\Http\Livewire\Admin\Subcategory;

use App\Models\Category;
use App\Models\Log;
use App\Models\SubCategory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $img;
    public $search;

    protected $queryString = ['search'];

    public $readyToLoad = false;

    public SubCategory $subcategory;

    public function mount()
    {
        $this->subcategory = new SubCategory();
    }



    protected $rules = [
        'subcategory.title' => 'required|min:3',
        'subcategory.name' => 'required',
        'subcategory.link' => 'required',
        'subcategory.parent' => 'required',
        'subcategory.status' => 'nullable',
    ];

    public function updated($title)
    {
        $this->validateOnly($title);
    }


    public function categoryForm()
    {
        $this->validate();
        if ($this->img) {
            $this->subcategory->img = $this->uploadImage();
        }
        $this->subcategory->save();
        if (!$this->subcategory->status) {
            $this->subcategory->update([
                'status' => 0
            ]);
        }
        Log::create([
            'user_id' => auth()->user()->id,
            'url' => 'افزودن زیر دسته' .'-'. $this->subcategory->title,
            'actionType' => 'ایجاد'
        ]);
        $this->emit('toast', 'success', ' زیردسته با موفقیت ایجاد شد.');

    }

    public function uploadImage()
    {
        $year = now()->year;
        $month = now()->month;
        $directory = "subcategory/$year/$month";
        $name = $this->img->getClientOriginalName();
        $this->img->storeAs($directory, $name);
        return "$directory/$name";
    }
    public function loadCategory()
    {
        $this->readyToLoad = true;
    }
    public function updateCategoryDisable($id)
    {
        $category = SubCategory::find($id);
        $category->update([
            'status' => 0
        ]);
        Log::create([
            'user_id' => auth()->user()->id,
            'url' => 'غیرفعال کردن وضعیت زیر دسته' .'-'. $category->title,
            'actionType' => 'غیرفعال'
        ]);
        $this->emit('toast', 'success', 'وضعیت زیر دسته با موفقیت غیرفعال شد.');
    }

    public function updateCategoryEnable($id)
    {
        $category = SubCategory::find($id);
        $category->update([
            'status' => 1
        ]);
        Log::create([
            'user_id' => auth()->user()->id,
            'url' => 'فعال کردن وضعیت زیر دسته' .'-'. $category->title,
            'actionType' => 'فعال'
        ]);
        $this->emit('toast', 'success', 'وضعیت زیر دسته با موفقیت فعال شد.');
    }

    public function deleteCategory($id)
    {
        $category = SubCategory::find($id);
        $category->delete();
        Log::create([
            'user_id' => auth()->user()->id,
            'url' => 'حذف کردن زیر دسته' .'-'. $category->title,
            'actionType' => 'حذف'
        ]);
        $this->emit('toast', 'success', ' زیر دسته با موفقیت حذف شد.');
    }


    public function render()
    {

        $categories = $this->readyToLoad ? SubCategory::where('title', 'LIKE', "%{$this->search}%")->
        orWhere('name', 'LIKE', "%{$this->search}%")->
        orWhere('link', 'LIKE', "%{$this->search}%")->
        orWhere('id', $this->search)->
        latest()->paginate(15) : [];
        return view('livewire.admin.subcategory.index',compact('categories'));
    }
}
