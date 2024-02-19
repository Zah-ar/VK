$(document).ready(function(){
    $('.btn').click(function(){
        var task = $(this).attr('task');
            if(task == 'waiting')
            {
                return false;
            }
        $(this).attr('task', 'waiting');
        $(this).addClass('disabled');
        $('.progress').removeClass('dis_no');
        step('GET_GOODS');
        /*$.ajax({
            url: '/index.php/?task='+task,
            type: 'GET',
            success: function (html) {
                $('#statusbar').html(html); // получаем ответ при успешном запросе
                allDone();
            }
        })*/
    })
})
function step(task)
{
//Прикрутить пошаговую обработку
    $.ajax({
        url: '/index.php/?task='+task,
        type: 'GET',
        success: function (html) {
            $('#statusbar').html(html); // получаем ответ при успешном запросе
                if(task == 'GET_GOODS')
                {
                    $('#statusbar').html('<div>Загрузка товаров из фида завершена</div>');
                    step('DELETE_GOODS');
                }
                else if(task == 'DELETE_GOODS')
                {
                    $('#statusbar').append('<div>Удаление товаров завершено...</div>');
                    step('UPDATE_GOODS');
                }
                else if(task == 'UPDATE_GOODS')
                {
                    $('#statusbar').append('<div>Обновление товаров завершено...</div>');
                    step('CREATE_GOODS');
                }else{
                    $('#statusbar').append('<div>Выгрузка новых товаров завершена...</div>');
                    allDone();
                }  
        }
    })

}
function allDone()
{
    $('#scan').attr('task', 'scan');
    $('#scan').removeClass('disabled');
    $('.progress').addClass('dis_no');
    $('#statusbar').append('<div style = "color:green; font-size:18px; font-weight:600">ALL DONE!</div>');
}