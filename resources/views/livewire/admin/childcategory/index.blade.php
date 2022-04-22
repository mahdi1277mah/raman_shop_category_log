@section('title','دسته های فرزند')
<div>
    <div class="main-content" wire:init="loadCategory">
        <div class="tab__box">
            <div class="tab__items">
                <a class="tab__item {{Request::routeIs('category.index') ? 'is-active': '' }}" href="/admin/category">دسته
                    ها</a>
                <a class="tab__item "
                   href="/admin/subcategory">زیر دسته ها</a>
                <a class="tab__item is-active "
                   href="/admin/childcategory">دسته های فرزند</a>

                |
                <a class="tab__item">جستجو: </a>

                <a class="t-header-search">
                    <form action="" onclick="event.preventDefault();">
                        <input wire:model.debounce.1000="search"
                               type="text" class="text" placeholder="جستجوی دسته ...">
                    </form>
                </a>

                <div class="position-relative d-lg-inline-block ">

                    <a class="tab__item btn btn-danger bottom-soft-delete"
                       href="{{route('childcategory.trashed')}}" style="color: white;margin-left: 10px">سطل زباله
                        ({{\App\Models\ChildCategory::onlyTrashed()->count()}})
                    </a>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-8 margin-left-10 margin-bottom-15 border-radius-3">

                <div class="table__box">
                    @if($readyToLoad)
                    <table class="table">

                        <thead role="rowgroup">
                        <tr role="row" class="title-row">
                            <th>آیدی</th>
                            <th>تصویر دسته فرزند</th>
                            <th>عنوان دسته فرزند</th>
                            <th>نام دسته فرزند</th>
                            <th>سر دسته فرزند</th>
                            <th>وضعیت دسته فرزند</th>
                            <th>عملیات</th>
                        </tr>
                        </thead>

                            <tbody>
                            @foreach($categories as $category)
                                <tr role="row">
                                    <td><a href="">{{$category->id}}</a></td>
                                    <td>
                                        <img src="/storage/{{$category->img}}" alt="img" width="100px">
                                    </td>
                                    <td><a href="">{{$category->title}}</a></td>
                                    <td><a href="">{{$category->name}}</a></td>
                                    <td><a href="">
                                            @foreach(\App\Models\SubCategory::where('id',$category->parent)->get() as $ca)
                                                {{$ca->title}}
                                            @endforeach
                                        </a></td>
                                    <td>
                                        @if($category->status == 1)
                                            <button wire:click="updateCategoryDisable({{$category->id}})"
                                                    type="submit" class="badge-success badge"
                                                    style="background-color: green">فعال
                                            </button>
                                        @else
                                            <button wire:click="updateCategoryEnable({{$category->id}})"
                                                    type="submit" class="badge-danger badge"
                                                    style="background-color: red">
                                                غیرفعال
                                            </button>
                                        @endif
                                    </td>
                                    <td>
                                        <a wire:click="deleteCategory({{$category->id}})" type="submit"
                                           class="item-delete mlg-15" title="حذف"></a>
                                        <a href="{{route('childcategory.update',$category)}}" class="item-edit "
                                           title="ویرایش"></a>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                            {{$categories->render()}}
                        @else



                            <div class="alert-warning alert">
                                در حال خواندن اطلاعات از دیتابیس ...
                            </div>


                        @endif


                    </table>
                </div>


            </div>
            <div class="col-4 bg-white">
                <p class="box__title">ایجاد زیر دسته جدید</p>
                <form wire:submit.prevent="categoryForm"
                      enctype="multipart/form-data" role="form"
                      class="padding-10 categoryForm">

                    @include('errors.error')
                    <div class="form-group">
                        <input type="text" wire:model.lazy="childcategory.title" placeholder="نام دسته "
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <input type="text" wire:model.lazy="childcategory.name" placeholder="نام انگلیسی دسته "
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <input type="text" wire:model.lazy="childcategory.link" placeholder="لینک دسته "
                               class="form-control">
                    </div>

                    <div class="form-group">
                        <div class="notificationGroup">
                            <input id="option4" type="checkbox" wire:model.lazy="childcategory.status" name="status"
                                   class="form-control">
                            <label for="option4">نمایش در دسته اصلی:</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <select wire:model.lazy="childcategory.parent" name="parent" id="" class="form-control">
                            <option value="">--لطفا سر دسته را انتخاب کنید--</option>
                        @foreach(\App\Models\SubCategory::all() as $category)
                                <option value="{{$category->id}}">{{$category->title}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="file" wire:model.lazy="img" class="form-control">
                        <span class="mt-2 text-danger" wire:loading wire:target="img">در حال آپلود ...</span>

                        <div wire:ignore class="progress mt-2" id="progressbar" style="display: none">
                            <div class="progress-bar" role="progressbar" style="width: 0%;">0%</div>
                        </div>
                    </div>
                    <div>
                        @if($img)
                            <img class="form-control mt-3 mb-3" width="400" src="{{$img->temporaryUrl()}}" alt="">
                        @endif
                    </div>

                    <button class="btn btn-brand">افزودن دسته</button>
                </form>
            </div>
        </div>


    </div>

    <script>
        document.addEventListener('livewire:load', () => {
            let progressSection = document.querySelector('#progressbar'),
                progressBar = progressSection.querySelector('.progress-bar');

            document.addEventListener('livewire-upload-start', () => {
                console.log('شروع آپلود');
                progressSection.style.display = 'flex';
            });
            document.addEventListener('livewire-upload-finish', () => {
                console.log('اتمام آپلود');
                progressSection.style.display = 'none';
            });
            document.addEventListener('livewire-upload-error', () => {
                console.log('ارور موقع آپلود');
                progressSection.style.display = 'none';
            });
            document.addEventListener('livewire-upload-progress', (event) => {
                console.log(`${event.detail.progress}%`);
                progressBar.style.width = `${event.detail.progress}%`;
                progressBar.textContent = `${event.detail.progress}%`;
            });

        });
    </script>
</div>
