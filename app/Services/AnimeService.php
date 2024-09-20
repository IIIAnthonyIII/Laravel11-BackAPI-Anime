<?php
namespace App\Services;
use App\Models\Anime;
use App\Services\Traits\Relations;
// use App\Models\Category;
// use App\Models\Tag;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnimeService {
    use Relations;

    //../api/anime?per_page=5&page=1
    public function getAll() {
        try {
            $query = $this->getDataQuery();
            if (request()->has('per_page')) {
                if (request()->get('per_page') <= 0) throw new \Exception('El parámetro per_page debe ser mayor a 0', 400);
                $anime = $query->paginate(request()->get('per_page'))->toArray();
            } else {
                $anime['data'] = $query->get()->toArray();
            }
            $anime['message'] = (sizeof($anime['data']) == 0) ? 'No hay animes' : 'Animes encontrados';
            return $anime;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    //../api/anime?status=A,I,E
    private function getDataQuery() {
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
        if (request()->query->count() > 0) $query = $this->parametersGet($query);
        return $query;
    }

    private function parametersGet($query) {
        $anime = new Anime();
        $params = [
            'fields' => 'fields',
            'embed' => 'embed',
            'sort' => 'sort'
        ];
        foreach ($params as $param => $method) {
            if (request()->has($param)) $query = $anime->{$method}($query, request()->get($param));
        }
        $queryParamsCopy = request()->query();
        $excludedParams = [
            'fields', 'embed', 'sort', 'search', 
            'per_page', 'page', 'status'
        ];
        foreach ($excludedParams as $param) {
            unset($queryParamsCopy[$param]);
        }
        if (count($queryParamsCopy) > 0) $query = $anime->parameters($query, $queryParamsCopy);
        return $query;
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
            $anime->image = $data->image;
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
                'dateOfIssue' => 'required'
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
            $anime->image = $data->image;
            $anime->episodes = $data->episodes;
            $anime->dateOfIssue = $data->dateOfIssue;
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

    public function delete($id, $data) {
        try {
            $anime = Anime::find($id);
            if ($anime == null) throw new \Exception('No existe este anime', 404);
            $anime->status = $data->permanent ? 'E' : 'I';
            $anime->user_delete = auth()->user()->id;
            $anime->date_delete = Carbon::now();
            $anime->update();
            return $anime;
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