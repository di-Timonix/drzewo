<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Drzewo</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <!-- <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- JavaScript Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous">
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
    <script src="./../resources/js/function.js"></script>


</head>

<body class="">

    <div class="container">

        <div class="container shadow py-4">
            <div class="text-center mx-auto">
                <div class="small fw-light">Szukaj w grupie</div>
                <div class="input-group">
                    <input class="form-control border-end-0 border" type="search" value="" placeholder="search" id="searching">
                </div>
            </div>


        </div>


        <div id="jstree">
            <ul>
                <li>Root node 1
                    <ul>
                        <li>Child node 1</li>
                        <li>Child node 1</li>
                        <li><a href="#">Child node 2</a></li>
                    </ul>
                </li>
                <li>Root node 1
                    <ul>
                        <li>Child node 1</li>
                        <li>Child node 1</li>
                        <li>Root node 1
                            <ul>
                                <li>Child node 1</li>
                                <li>Child node 1</li>
                                <li><a href="#">Child node 2</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>

        <div class="container shadow py-4">

            <div class="container p-3 mb-4">
                <div class="treeMessage"></div>
            </div>
           
            <div class="mx-auto text-center">
                <button class="btn btn-danger">Zapisz Drzewo</button>

                <div id="sortAll" class="btn btn-secondary">sort</div>
                <div id="showAll" class="btn btn-primary">rozwiń</div>
                <div id="addNode" type="button" class="btn btn-warning">Dodaj Drzewo</div>
            </div>

        </div>

        <script>
            window.addEventListener("DOMContentLoad", dzewo());
        </script>
    </div>

</body>

</html>