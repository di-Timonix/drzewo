function dzewo() {
    $(function () {

        let dellNode = [];
        let addNode = [];
        let updateNode = [];

        let sortType = "asc";

        $('#jstree').jstree({
            "core": {
                "multiple": true,
                "animation": 100,

                "check_callback": true,
                'data': {
                    'url': function (node) {
                        return 'api';
                    },
                    'data': function (node) {
                        return {
                            'parent': node.id
                        };
                    }
                }
                ,
                "themes": {
                    "responsive": true
                },
                "check_callback": true,
            },

            "types": {
                "default": {
                    "icon": "fa fa-folder text-primary"
                },
                "file": {
                    "icon": "fa fa-file  text-primary"
                }
            },
            sort: function (a, b) {
                a1 = this.get_node(a);
                b1 = this.get_node(b);
                if (sortType === "asc") {
                    return (a1.text > b1.text) ? 1 : -1;
                } else {
                    return (a1.text > b1.text) ? -1 : 1;
                }
            },

            "plugins": [
                "contextmenu",
                "dnd",
                "massload",
                "search",
                "sort",
                "state",
                "types",
                "unique",
                "changed",
                "conditionalselect"
            ],
            "search": {
                "fuzzy": true,
                "show_only_matches": true
            }

        }).on('create_node.jstree', function (e, data) {
            addNode.push({
                'id': data.node.id,
                'position': data.node.parent,
                'text': data.node.text,
                'parent': data.parent,
            });

        }).on('rename_node.jstree', function (e, data) {
            const iconeNode = $("#jstree").find("#" + data.node.id +
                " .jstree-icon.jstree-themeicon");
            if (data.text.indexOf(".") !== -1) {
                iconeNode.addClass("fa-file").removeClass("fa-folder")
            } else {
                iconeNode.addClass("fa-folder").removeClass("fa-file");
            }

            if (data.old !== data.text) {
                updateNode.push({
                    'id': data.node.id,
                    'text': data.text,
                    'parent': data.node.parent,
                });
            }

        }).on('delete_node.jstree', function (e, data) {
            dellNode.push({
                'id': data.node.id,
                'parent': data.node.parent,
            });
            console.log(dellNode);
            document.querySelector(".treeMessage").innerHTML =
                "Pod czas usuwania elementu zostanie też usunięta cała jego zawartość!"
        });

        function setSortFunction(sort) {
            sortType = sort;
        }

        document.getElementById("sortAll").addEventListener("click", (e) => {
            if (sortType === "asc") {
                setSortFunction('desc');
                document.getElementById("sortAll").innerText = "Sort: desc";
            } else {
                setSortFunction('asc');
                document.getElementById("sortAll").innerText = "Sort: asc";
            }

            $("#jstree").jstree(true).refresh();
        });


        let isHide = true;
        document.getElementById("showAll").addEventListener("click", (e) => {
            if (isHide === true) {
                isHide = false;
                $("#jstree").jstree('open_all');
                document.getElementById("showAll").innerText = "Zwiń całe drzewo!";
            } else {
                isHide = true;
                $("#jstree").jstree('close_all');
                document.getElementById("showAll").innerText = "Rozwiń całe drzewo!";
            }
        });

        document.getElementById("addNode").addEventListener("click", (e) => {
            $("#jstree").jstree("create_node", null);
        });

        let to = false;
        $('#searching').keyup(function () {
            if (to) {
                clearTimeout(to);
            }
            to = setTimeout(function () {
                var v = $('#searching').val();
                $('#jstree').jstree(true).search(v);
            }, 250);
        });

        $('button').on('click', function () {
            const saveData = ($.jstree.reference("#jstree").get_json('#', {
                flat: true
            }));

            const sendData = JSON.stringify({
                addNode,
                updateNode,
                dellNode,
                saveData
            });

            $.ajax({
                type: "POST",
                url: "api/update",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                "contentType": 'application/json; charset=utf-8',
                data: sendData
            })
                .done(function (info) {
                    document.querySelector(".treeMessage").innerHTML = "Wysyłanie...";
                    console.log("ajax ok");

                    if (info.status === "warning") {

                        document.querySelector(".treeMessage").innerHTML = `
                            <div class="alert alert-warning" role="alert">${info.msg}</div>
                        `;
                    } else if (info.status === "success") {

                        document.querySelector(".treeMessage").innerHTML = `
                            <div class="alert alert-success" role="alert">${info.msg}</div>
                        `;
                    } else if (info.status === "info") {

                        document.querySelector(".treeMessage").innerHTML = `
                            <div class="alert alert-info" role="alert">${info.msg}</div>
                        `;
                    } else if (info.status === "error") {

                        document.querySelector(".treeMessage").innerHTML = `
                            <div class="alert alert-error" role="alert">${info.msg}</div>
                        `;
                    }

                    if ("ok" in info.data && info.data.lentgh !== 0) {
                        if ("add" in info.data["ok"]) {
                            info.data["ok"]["add"].forEach(element => {
                                addNode.splice(element.poz, 1);
                            });

                            document.querySelector(".treeMessage").innerHTML = `
                                <div class="alert alert-info" role="alert">Dodano Nowy Zasób Poprawnie</div>
                            `;
                        }

                        if ("rename" in info.data["ok"]) {

                            info.data["ok"]["rename"].forEach(element => {
                                updateNode.splice(element.poz, 1);
                            });

                            document.querySelector(".treeMessage").innerHTML = `
                                <div class="alert alert-info" role="alert">Zmieniono Nazwę Poprawnie</div>
                            `;
                        }

                        if ("dell" in info.data["ok"]) {
                            info.data["ok"]["dell"].forEach(element => {
                                dellNode.splice(element.poz, 1);
                            });

                            document.querySelector(".treeMessage").innerHTML = `
                                <div class="alert alert-info" role="alert">Usunięto Zasób Poprawnie</div>
                            `;
                        }

                        addNode = [];
                        updateNode = [];
                        dellNode = [];
                    }

                    if ("error" in info.data) {
                        info.data["error"].forEach(element => {
                            document.getElementById(element.id).style.backgroundColor = "red";
                            document.querySelector(".treeMessage").innerHTML = `
                                <div class="alert alert-info" role="alert">${element.text} - ${element.mess}</div>
                            `;

                            addNode = [];
                            updateNode = [];
                            dellNode = [];
                        });
                    }

                })
                .fail(function () {
                    alert("Wystąpił błąd. Spróbuj ponownie później");
                });
        });
    });
}