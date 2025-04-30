@switch($type)
    @case('text')
        <input type="text" name="{{$name}}" class="form-control" placeholder="{{$label}}" value="{{$data}}">
        @break
    @case('image')
        <img src="{{'/storage/'.$data}}" alt="{{$label}} image" class="d-block w-100 rounded-3 mb-2" style="max-width: 5em;">
        <input type="file" name="{{$name}}" class="form-control" placeholder="{{$label}}">
        @break
    @case('number')
        <input type="number" name="{{$name}}" class="form-control" placeholder="{{$label}}" value="{{$data}}">
        @break
    @case('date')
        <input type="date" name="{{$name}}" class="form-control" placeholder="{{$label}}" value="{{$data}}">
        @break
    @case('color')
        <input type="color" name="{{$name}}" class="form-control" placeholder="{{$label}}" value="{{$data}}">
        @break
    @case('textarea')
        <textarea name="{{$name}}" id="{{$name}}" cols="30" rows="5" class="form-control" placeholder="{{$label}}">{{$data}}</textarea>
        @break
    {{-- @case('select')
        <select name="{{$name}}" id="{{$name}}" class="form-select">
          @foreach ($options as $key => $value)
              <option value="{{$key}}">{{$value}}</option>
          @endforeach
        </select>
        @break --}}
    @case('toggle')
        <div class="form-check form-switch py-2">
            <input class="form-check-input" name="{{$name}}" type="checkbox" id="{{$name}}" value="1" {{$data ? 'checked' : ''}} />
            <label class="form-check-label" for="{{$name}}">{{$label}}</label>
        </div>
        @break
    @case('multiple_image')
        <div class="d-flex align-items-start gap-2">
            @php
                $datas = json_decode($data);
            @endphp
            @foreach($datas as $dt)
                <img src="{{'/storage/'.$dt}}" alt="{{$label}} image" class="d-block w-100 rounded-3 mb-2" style="max-width: 5em;">
            @endforeach
        </div>
        <input type="file" name="{{$name}}" class="form-control" placeholder="{{$label}}">
        @break
    @case('multiple_file')
        <div class="row mb-2">
            @php
                $datas = json_decode($data);
            @endphp
            @foreach($datas as $dt)
            <div class="col-md-6">
              <a href="{{'/storage/'.$data}}" target="_blank" class="bg-primary-subtle p-2 px-3 rounded-3">
                File : {{$dt}}
              </a>
            </div>
            @endforeach
        </div>
        <input type="file" name="{{$name}}" class="form-control" placeholder="{{$label}}">
        @break
    @case('file')
        <div class="row mb-2">
            <div class="col-md-6">
                <a href="{{'/storage/'.$data}}" target="_blank" class="bg-primary-subtle p-2 px-3 rounded-3">
                    File : {{$data}}
                </a>
            </div>
        </div>
        <input type="file" name="{{$name}}" class="form-control" placeholder="{{$label}}">
        @break
    @default
        <input type="text" name="{{$name}}" class="form-control" placeholder="{{$label}}">
@endswitch