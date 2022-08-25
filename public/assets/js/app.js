$(".checkbox-big").on('click', function(e) {
    if ($(".all-permissions .checkbox-big").prop("checked") == true) {
        // $(".all-permissions").removeClass("hide-block");
        $(".selective-permissions").addClass("hide-block");
    }   else {
        // $(".all-permissions").addClass("hide-block");
        $(".selective-permissions").removeClass("hide-block");
    }
});

/* function confirmationDelete(anchor) {
    swal({
        title: "Are you sure want to delete this row?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            window.location = anchor.attr("href");
        }
    });
    //   var conf = confirm("Are you sure want to delete this User?");
    //   if (conf) window.location = anchor.attr("href");
} */

$(document).on('click', '.delete_block', function(e) {
    swal({
        title: "Are you sure want to delete this field?",
        text: "Once deleted, you will not be able to recover this data!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            let id = $(this).data("block");
            $(`#block-${id}`).remove();
        }
    });

});

$(document).ready(function() {
    setTimeout(() => {
        $(".alert").fadeOut();
    }, 3000);
});



// validate adhar card number
function checkUID(e) {
    /*e.preventDefault();*/ var uid = $("#aadhaar_number").val();
    /*console.log(uid);*/ if (uid.length != 12) {
        return false;
    }
    var Verhoeff = {
        d: [
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            [1, 2, 3, 4, 0, 6, 7, 8, 9, 5],
            [2, 3, 4, 0, 1, 7, 8, 9, 5, 6],
            [3, 4, 0, 1, 2, 8, 9, 5, 6, 7],
            [4, 0, 1, 2, 3, 9, 5, 6, 7, 8],
            [5, 9, 8, 7, 6, 0, 4, 3, 2, 1],
            [6, 5, 9, 8, 7, 1, 0, 4, 3, 2],
            [7, 6, 5, 9, 8, 2, 1, 0, 4, 3],
            [8, 7, 6, 5, 9, 3, 2, 1, 0, 4],
            [9, 8, 7, 6, 5, 4, 3, 2, 1, 0],
        ],
        p: [
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            [1, 5, 7, 6, 2, 8, 3, 0, 9, 4],
            [5, 8, 0, 3, 7, 9, 6, 1, 4, 2],
            [8, 9, 1, 6, 0, 4, 3, 5, 2, 7],
            [9, 4, 5, 3, 1, 2, 6, 8, 7, 0],
            [4, 2, 8, 6, 5, 7, 3, 9, 0, 1],
            [2, 7, 9, 3, 8, 0, 6, 4, 1, 5],
            [7, 0, 4, 6, 9, 1, 3, 2, 5, 8],
        ],
        j: [0, 4, 3, 2, 1, 5, 6, 7, 8, 9],
        check: function (str) {
            var c = 0;
            str.replace(/\D+/g, "")
                .split("")
                .reverse()
                .join("")
                .replace(/[\d]/g, function (u, i) {
                    c = Verhoeff.d[c][Verhoeff.p[i % 8][parseInt(u, 10)]];
                });
            return c;
        },
        get: function (str) {
            var c = 0;
            str.replace(/\D+/g, "")
                .split("")
                .reverse()
                .join("")
                .replace(/[\d]/g, function (u, i) {
                    c = Verhoeff.d[c][Verhoeff.p[(i + 1) % 8][parseInt(u, 10)]];
                });
            return Verhoeff.j[c];
        },
    };

    String.prototype.verhoeffCheck = (function () {
        var d = [
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            [1, 2, 3, 4, 0, 6, 7, 8, 9, 5],
            [2, 3, 4, 0, 1, 7, 8, 9, 5, 6],
            [3, 4, 0, 1, 2, 8, 9, 5, 6, 7],
            [4, 0, 1, 2, 3, 9, 5, 6, 7, 8],
            [5, 9, 8, 7, 6, 0, 4, 3, 2, 1],
            [6, 5, 9, 8, 7, 1, 0, 4, 3, 2],
            [7, 6, 5, 9, 8, 2, 1, 0, 4, 3],
            [8, 7, 6, 5, 9, 3, 2, 1, 0, 4],
            [9, 8, 7, 6, 5, 4, 3, 2, 1, 0],
        ];
        var p = [
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
            [1, 5, 7, 6, 2, 8, 3, 0, 9, 4],
            [5, 8, 0, 3, 7, 9, 6, 1, 4, 2],
            [8, 9, 1, 6, 0, 4, 3, 5, 2, 7],
            [9, 4, 5, 3, 1, 2, 6, 8, 7, 0],
            [4, 2, 8, 6, 5, 7, 3, 9, 0, 1],
            [2, 7, 9, 3, 8, 0, 6, 4, 1, 5],
            [7, 0, 4, 6, 9, 1, 3, 2, 5, 8],
        ];

        return function () {
            var c = 0;
            this.replace(/\D+/g, "")
                .split("")
                .reverse()
                .join("")
                .replace(/[\d]/g, function (u, i) {
                    c = d[c][p[i % 8][parseInt(u, 10)]];
                });
            return c === 0;
        };
    })();

    if (Verhoeff["check"](uid) === 0) {
        /*return true;*/
        alert("Match Found..!");
    } else {
        /*return false;*/
        alert("Match Not Found..!");
    }
}