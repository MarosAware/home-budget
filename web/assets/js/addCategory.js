// $(function() {
//
//     var addCat = $('#addCategory');
//
//     addCat.on('click', function() {
//
//         var form = $('.make_hidden');
//         var year = $('.hidden_inputs').eq(0).val();
//         var month = $('.hidden_inputs').eq(1).val();
//
//         // console.log(year);
//         // console.log(month);
//
//         form.slideToggle();
//         var formOnly = form.children().eq(1);
//
//
//         formOnly.on('submit', function(event) {
//             event.preventDefault();
//
//             var catName = $('#appbundle_category_name').val();
//             var catType = $('#appbundle_category_type').val();
//             // console.log(catName);
//             // console.log(catType);
//
//             var data = {
//                 "name": catName,
//                 "type": catType
//             };
//
//             // console.log(this);
//             // var url = "{{ path('http://localhost:8000/2018/02/addCategory/') }}";
//             //var data = new FormData(this);
//
//
//             // var data = $(this).serialize();
//
//
//
//             //'http://localhost:8000/' + year + '/' + month + '/addCategory/',
//             var path = path('app_category_addcategory', {'year':" + year + ", 'month': " + month + " );
//
//             $.ajax({
//                 url: "{{ path('app_category_addcategory', {'year':" + year + ", 'month': " + month + " ) }}",
//                 data: data,
//                 type: "POST"
//
//             }).done(function(result) {
//                 console.log(result);
//             }).fail(function(xhr,status,err) {
//
//             })
//
//
//
//         });
//     })
//
// });
