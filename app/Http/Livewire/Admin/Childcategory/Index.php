<?php

namespace App\Http\Livewire\Admin\Childcategory;

use App\Models\ChildCategory;
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

    public ChildCategory $childcategory;

    public function mount()
    {
        $this->childcategory = new ChildCategory();
    }



    protected $rules = [
        'childcategory.title' => 'required|min:3',
        'childcategory.name' => 'required',
        'childcategory.link' => 'required',
        'childcategory.parent' => 'required',
        'childcategory.status' => 'nullable',
    ];

    public function updated($title)
    {
        $this->validateOnly($title);
    }


    public function categoryForm()
    {
        $this->validate();
        if ($this->img) {
            $this->childcategory->img = $this->uploadImage();
        }
        $this->childcategory->save();
        if (!$this->childcategory->status) {
            $this->childcategory->update([
                'status' => 0
            ]);
        }
        Log::create([
            'user_id' => auth()->user()->id,
            'url' => 'افزودن دسته فرزند' .'-'. $this->childcategory->title,
            'actionType' => 'ایجاد'
        ]);
        $this->emit('toast', 'success', ' دسته فرزند با موفقیت ایجاد شد.');

    }

    public function uploadImage()
    {
        $year = now()->year;
        $month = now()->month;
        $directory = "childcategory/$year/$month";
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
        $category = ChildCategory::find($id);
        $category->update([
            'status' => 0
        ]);
        Log::create([
            'user_id' => auth()->user()->id,
            'url' => 'غیرفعال کردن وضعیت دسته فرزند' .'-'. $category->title,
            'actionType' => 'غیرفعال'
        ]);
        $this->emit('toast', 'success', 'وضعیت دسته فرزند با موفقیت غیرفعال شد.');
    }

    public function updateCategoryEnable($id)
    {
        $category = ChildCategory::find($id);
        $category->update([
            'status' => 1
        ]);
        Log::create([
            'user_id' => auth()->user()->id,
            'url' => 'فعال کردن وضعیت دسته فرزند' .'-'. $category->title,
            'actionType' => 'فعال'
        ]);
        $this->emit('toast', 'success', 'وضعیت دسته فرزند با موفقیت فعال شد.');
    }

    public function deleteCategory($id)
    {
        $category = ChildCategory::find($id);
        $category->delete();
        Log::create([
            'user_id' => auth()->user()->id,
            'url' => 'حذف کردن دسته فرزند' .'-'. $category->title,
            'actionType' => 'حذف'
        ]);
        $this->emit('toast', 'success', ' دسته فرزند با موفقیت حذف شد.');
    }


    public function render()
    {

        $categories = $this->readyToLoad ? ChildCategory::where('title', 'LIKE', "%{$this->search}%")->
        orWhere('name', 'LIKE', "%{$this->search}%")->
        orWhere('link', 'LIKE', "%{$this->search}%")->
        orWhere('id', $this->search)->
        latest()->paginate(15) : [];
        return view('livewire.admin.childcategory.index',compact('categories'));
    }
}
