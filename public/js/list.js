let oldStatusId;

function updateTaskStatus(taskId) {
    const statusElement = document.getElementById(taskId);
    const rawTaskId = taskId.split("-")[2];
    oldStatusId = taskId;
    getAjax("/update/task/" + rawTaskId + "/status/" + statusElement.value, postUpdateTaskStatus)
}

function postUpdateTaskStatus(data) {
    if(parseInt(data.error) === 1) {
        if(data.message === "Status already in use") {
            const statusElement = document.getElementById(oldStatusId);
            for(let i = 0; i < statusElement.options.length; i++) {
                if(parseInt(statusElement.options[i].value) === data.error_data.old_task_status) {
                    statusElement.options[i].selected = true;
                    break;
                }
            }
        }
    }
    handleNotification(data);
}

function updateTaskAssign(taskId) {
    const assignElement = document.getElementById(taskId);
    const rawTaskId = taskId.split("-")[2];
    getAjax("/update/task/" + rawTaskId + "/assign/" + assignElement.value, handleNotification)
}

function handleNotification(data) {
    if(parseInt(data.error) === 1) {
        if(data.error_data.length > 0) {
            console.error(data.data);
        }
        addNotification("error", data.message)
    } else {
        addNotification("success", data.message)
    }
}

function addNotification(type, text) {
    const notification = document.getElementById("notification");
    const alert = document.getElementById("alert");
    notification.innerHTML = text;
    if(type === "error") {
        alert.className = "alert alert-danger";
    } else if(type === "success") {
        alert.className = "alert alert-success";
    }

    $("#alert").show().delay(5000).fadeOut();
}