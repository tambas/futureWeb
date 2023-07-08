<!DOCTYPE html>
<html>
<head>
    <title>Support</title>
    <script src="https://code.jquery.com/jquery-2.2.2.js" integrity="sha256-4/zUCqiq0kqxhZIyp4G0Gk+AOtCJsY1TA00k5ClsZYE=" crossorigin="anonymous"></script>
    <style>
        #support {
            width: 400px;
        }

        #support .part {
            margin: 5px;
            border: black 1px solid;
        }

        #support .character {
            margin: 5px;
            border: #009688 2px solid;
            border-radius: 5px;
            height: 50px;
        }

        #support .character:hover {
            border-color: #00564F;
            background-color: #E3FFFC;
        }

        #support .character img {
            vertical-align:middle
        }
    </style>
</head>
<body>
    <form id="support" action="/support/store" method="post">
    </form>
</body>
<script>

var nb_part = 0;

function get_child(id, name, params) {
    remove_parts(id);

    var url = '/support/child/' + name;
    if (params != null && params != '') url +=  '/' + params;

    $.get(url, function(data) {
        $('#support').append('<div class="part" part="' + id + '">' + data + '</div>');
        nb_part = id + "";
    });
}

function remove_parts(id) {
    if (nb_part >= id) {
        for (var i = id; i <= nb_part; i++) {
            $('#support [part='+i+']').remove();
        }
        nb_part = id;
    }
}

// On field change
$('#support').on('change', 'select, input[type=radio]', function() {
    data = $(this).val().split('|');
    id = parseInt($(this).parent().attr('part'));
    tag = data[0];

    if (tag == 'c') // child
    {
        child = (data[1] != null ? data[1] : '');
        params = (data[2] != null ? data[2] : '');

        get_child(id + 1, child, params);
    }
    else if (tag == 'r') // reset
    {
        remove_parts(id + 1);
    }
});

get_child(1, 'support');

</script>
</html>
