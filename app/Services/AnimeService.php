<?php
namespace App\Services;
use App\Models\Anime;
use App\Services\Traits\HasRelations;
// use App\Models\Category;
// use App\Models\Tag;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnimeService {
    use HasRelations;

    public function getAll($data) {
        try {
            $query = $this->getDataQuery($data);
            if (request()->has('per_page')) {
                (request()->get('per_page') <= 0) 
                  ? throw new \Exception('El parámetro per_page debe ser mayor a 0', 400) 
                  : $anime = $query->paginate(request()->get('per_page'))->toArray();
            } else {
                $anime['data'] = $query->get()->toArray();
            }
            $anime['message'] = (sizeof($anime['data']) == 0) ? 'No hay animes' : 'Animes encontrados';
            return $anime;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function getDataQuery($data) {
        if (auth()->check()) {
            if (request()->has('status')) {
                $query = new Anime();
                $statuses = array_map('trim', explode(',', request()->get('status')));
                foreach ($statuses as $status) {
                    $query =  $query->orWhere('status', $status);
                }
            } else {
                $query = Anime::where('status', '!=', 'E');
            }
        } else {
            $query = Anime::where('status', 'A');
        }
        // if (isset($_GET) && count($_GET) > 0) {
        //     $query = $this->parametersGet($query, $data);
        // }
        return $query;
    }

    private function parametersGet($query, $data) {
        $manual = new Anime();
        $manualQuery = $query;

        if (isset($_GET['fields'])) {
            $manualQuery = $manual->fields($query, $_GET['fields']);
        }

        if (isset($_GET['slug'])) {
            $manualQuery = $manual->searchBySlug($query, $_GET['slug']);
        }

        if (isset($_GET['embed'])) {
            $manualQuery = $manual->embed($query, $_GET['embed']);
        }

        if (isset($_GET['search'])) {
            $manualQuery = $manual->search($query, $_GET['search']);
        }

        if (isset($_GET['sort'])) {
            $manualQuery = $manual->sort($query, $_GET['sort']);
        }

        if (isset($_GET['searchAll'])) {
            $manualQuery = $manual->searchAll($query, $_GET['searchAll'], 'manual');
        }

        unset(
            $_GET['fields'],
            $_GET['embed'],
            $_GET['sort'],
            $_GET['search'],
            $_GET['per_page'],
            $_GET['page'],
            $_GET['status'],
            $_GET['slug'],
            $_GET['searchAll']
        );
        if (isset($_GET) && count($_GET) > 0) {
            $manualQuery = $manual->parameters($query, $_GET);
        }
        return $manualQuery;
    }

    public function getId($data, $id) {
        try {
            $anime = new Anime();
            $query = (auth()->user())
              ? $anime->where('id', $id)->where('status', '!=', 'E')
              : $anime->where('id', $id)->where('status', 'A');
            if (isset($data->fields)) $query = $anime->fields($query, $data->fields);
            if (isset($_GET['embed'])) $query = $anime->embed($query, $_GET['embed']);
            return $query->first();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function create($data) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($data->all(), [
                'title' => 'required|string|max:100',
                'episodes' => 'required',
                'dateOfIssue' => 'required'
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                throw new \Exception($error, 400);
            }
            $animeTitle = Anime::where('title', $data->title)->first();
            if ($animeTitle != null) throw new \Exception('El anime ya existe, por favor ingrese uno nuevo', 409);
            $anime = new Anime();
            $anime->title = $data->title;
            $anime->name = $data->name;
            $anime->episodes = $data->episodes;
            $anime->dateOfIssue = $data->dateOfIssue;
            $anime->user_create = auth()->user()->id;
            $anime->save();
            DB::commit();
            return  $anime;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    // public function base64_to_jpeg($base64_string, $output_file) {
    //     // open the output file for writing
    //     $ifp = fopen($output_file, 'wb');
    //     // split the string on commas
    //     // $data[ 0 ] == "data:image/png;base64"
    //     // $data[ 1 ] == <actual base64 string>
    //     $data = explode(',', $base64_string);
    //     // we could add validation here with ensuring count( $data ) > 1
    //     fwrite($ifp, base64_decode($data[1]));
    //     // clean up the file resource
    //     fclose($ifp);
    //     return $output_file;
    // }


    public function update($data, $id) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($data->all(), [
                'title' => 'required|string|max:100',
                'episodes' => 'required',
                'dateOfIssue' => 'required',
                'status' => 'required|string|max:1'
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                throw new \Exception($error);
            }
            $anime = Anime::find($id);
            if ($anime == null) throw new \Exception('No existe este anime', 404);
            if ($anime->title != $data->title) {
                $animeTitle = Anime::where('title', $data->title)->first();
                if ($animeTitle != null) throw new \Exception('El anime ya existe, por favor ingrese uno nuevo', 409);
            }
            $anime->title = $data->title;
            $anime->name = $data->name;
            $anime->episodes = $data->episodes;
            $anime->dateOfIssue = $data->dateOfIssue;
            $anime->status = $data->status;
            $anime->user_modifies = auth()->user()->id;

            // $manual->categories()->detach();
            // if ($data->categories != null) {
            //     $this->addRelations($manual->categories(), $data->categories, new Category(), 'update');
            // }

            // $manual->tags()->detach();
            // if ($data->tags != null) {
            //     $this->addRelations($manual->tags(), $data->tags, new Tag(), 'update');
            // }
            
            // $manual->categories;
            // $manual->tags;
            $anime->update();
            DB::commit();
            return $anime;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function delete($id) {
        try {
            $anime = Anime::find($id);
            if ($anime == null) throw new \Exception('No existe este anime', 404);
            $anime->status = 'I';
            $anime->user_delete = auth()->user()->id;
            $anime->date_delete = Carbon::now();
            $anime->update();
            return $anime;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function activar($id) {
        DB::beginTransaction();
        try {
            $query = Anime::find($id);
            if ($query == null) throw new \Exception('No existe este anime', 404);
            $query->status = 'A';
            $query->update();
            DB::commit();
            return $query;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function deleteStatusE($id) {
        try {
            $query = Anime::find($id);
            if ($query == null) throw new \Exception('No existe este anime', 404);
            $query->status = 'E';
            $query->date_delete = Carbon::now();
            $query->user_delete = auth()->user()->id;
            $query->update();
            // //buscamos las relaciones con section y las eliminamos
            // $sections = $query->sections()->get();
            // foreach ($sections as $section) {
            //     $section->status = 'E';
            //     $section->date_delete = Carbon::now();
            //     $section->user_delete = auth()->user()->id;
            //     $section->update();
            //     //buscamos las relaciones con subsections y las eliminamos
            //     $subsections = $section->subsections()->get();
            //     foreach ($subsections as $subsection) {
            //         $subsection->status = 'E';
            //         $subsection->date_delete = Carbon::now();
            //         $subsection->user_delete = auth()->user()->id;
            //         $subsection->update();
            //         //buscamos las relaciones con steps y las eliminamos
            //         $steps = $subsection->steps()->get();
            //         foreach ($steps as $step) {
            //             $step->status = 'E';
            //             $step->date_delete = Carbon::now();
            //             $step->user_delete = auth()->user()->id;
            //             $step->update();
            //             //buscamos las relaciones con files y las eliminamos
            //             $files = $step->files()->get();
            //             foreach ($files as $file) {
            //                 $file->status = 'E';
            //                 $file->date_delete = Carbon::now();
            //                 $file->user_delete = auth()->user()->id;
            //                 $file->update();
            //                 //eliminamos de s3
            //                 $s3 = Storage::disk('s3');
            //                 $s3->delete($file->path);
            //             }
            //         }
            //     }
            // }
            return $query;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function getSlug($title) {
        $valorRandom = uniqid();
        $slug = Str::of($title)->slug('-')->limit(255 - mb_strlen($valorRandom) - 1, '')->trim('-')->append('-', $valorRandom);
        return $slug;
    }
}