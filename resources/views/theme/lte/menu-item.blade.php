@if ($item["submenu"] == [])
    <li class="nav-item">
        <a href="{{url($item['url'])}}" class="nav-link {{getMenuActivo($item["url"])}}">
            <i class="nav-icon fa {{$item["icono"]}}"></i>
            <p style=@if ($submenu ?? '' == 1) "color: DodgerBlue;" @else "" @endif>
				@if ($submenu ?? '' == 1)
                	&nbsp {{ $item["nombre"] }}
				@else
                	{{ $item["nombre"] }}
				@endif
            </p>
        </a>
    </li>
@else
    <li class="nav-item has-treeview">
        <a href="javascript:;" class="nav-link">
          <i class="nav-icon fa {{$item["icono"]}}"></i>
          <p style=@if ($submenu ?? '' == 1) "color: DodgerBlue;" @else "" @endif>
            @if ($submenu ?? '' == 1)
                &nbsp
            @endif
            {{$item["nombre"]}}
            <i class="right fas fa-angle-left"></i>
          </p>
        </a>
        <ul class="nav nav-treeview">
            @foreach ($item["submenu"] as $submenu)
                @include("theme.$theme.menu-item", ["item" => $submenu, "submenu" => 1])
            @endforeach
        </ul>
    </li>
@endif
