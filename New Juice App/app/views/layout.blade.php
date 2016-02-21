<html>
<head>
    <title></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/0.3.2/marked.min.js"></script>
</head>

<body class="body-<?php echo (Route::current()->getUri() == "/" ? "login" : Route::current()->getUri()) ?>">

    {{ HTML::style('css/bootstrap-theme.min.css') }} {{ HTML::style('css/bootstrap.css') }} {{ HTML::script('js/jquery-1.11.1.min.js') }} {{ HTML::script('js/bootstrap.min.js') }} {{ HTML::script('js/bootstrap-dialog.min.js')}}
	{{ HTML::style('css/custom.css')  }}

    <div class="container-fluid">

    <?php if (Auth::check()): ?>

        <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
            <div class="container-fluid">
                <!-- Brand and toggle get grouped for better mobile display -->

                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"><span class="sr-only">Toggle navigation</span> </button>
                </div><!-- Collect the nav links, forms, and other content for toggling -->

                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <li><a href="/dashboard">Dashboard <span class="sr-only">Dashboard</span></a></li>
                        @if(Auth::user()->group->id < 3)
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Ingredients</a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/ingredients/">List Ingredients</a></li>
                                @if(Auth::user()->group->id == 1)
                                <li><a href="/ingredients/create">Add Ingredient</a></li>
                                @endif
                            </ul>

                        </li>
                        @endif
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Flavors</a>

                            <ul class="dropdown-menu" role="menu">

                                <li><a href="/flavors/">List Flavors</a></li>
                                @if(Auth::user()->group->id == 1)
                                <li><a href="/flavors/create">Add Flavor</a></li>
                                @endif
                            </ul>
                        </li>
                        @if(Auth::user()->group->id < 3)
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Manufacturers</a>

                            <ul class="dropdown-menu" role="menu">

                                <li><a href="/manufacturers/">List Manufacturers</a></li>

                                @if(Auth::user()->group->id == 1)
                                <li><a href="/manufacturers/create">Add Manufacturer</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if(Auth::user()->group->id < 3)
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Brands</a>

                            <ul class="dropdown-menu" role="menu">

                                <li><a href="/brands/">List Brands</a></li>

                                @if(Auth::user()->group->id == 1)
                                <li><a href="/brands/create">Add Brand</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if(Auth::user()->group->id == 1)
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Users</a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/users/">List Users</a></li>

                                <li><a href="/users/create">Add User</a></li>
                            </ul>
                        </li>
                        @endif
                    </ul>

                   <!--
 <form class="navbar-form navbar-left" role="search">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search">
                        </div><button type="submit" class="btn btn-default">Submit</button>
                    </form>
-->

                    <ul class="nav navbar-nav navbar-right">
                        {{--<li><a href="#">Link</a></li>--}}

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">My Account</a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="/users/<?php echo Auth::user()->id; ?>/edit">Edit My Account</a></li>
								
                                <li><a href="/logout/">Logout</a></li>

                            </ul>
                        </li>
                    </ul>
                </div><!-- /.navbar-collapse -->
            </div><!-- /.container-fluid -->
        </nav>
        <?php endif; ?>

        <div class="container">
            @if(Session::has('message'))

            <p class="alert">{{ Session::get('message') }}</p>@endif
        </div>@yield('content')
    </div>
    {{--{{ HTML::script('react/react.js') }}--}}
    {{--{{ HTML::script('react/JSXTransformer.js') }}--}}
    {{HTML::script('js/dist/bundle.js')}}
</body>
</html>
