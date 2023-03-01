const editForm = document.getElementById('edit-users')
const firstNameInput =  document.querySelector('[name="user_fname"]');
const lastNameInput =  document.querySelector('[name="user_lname"]');
const positionInput =  document.querySelector('[name="position"]');
const submitBtn = document.getElementById('submit');
const usersTable = document.querySelector('#users tbody');
let tableRowsNodes = Array.prototype.slice.call( usersTable.children );
let delBTns = document.querySelectorAll('.delete');

document.addEventListener("click", async function(e){
    let action = e.target.className;
    tableRowsNodes = Array.prototype.slice.call( usersTable.children );
    delBTns = document.querySelectorAll('.delete');
    if (action === 'edit') {
        let userFirstName = e.target.parentElement.querySelector('.first-name').textContent;
        let userLastName = e.target.parentElement.querySelector('.last-name').textContent;
        let userPosition = e.target.parentElement.querySelector('.position').dataset.value;
        let userId = tableRowsNodes.indexOf( e.target.parentElement );
        console.log(userId);
        firstNameInput.value = userFirstName;
        lastNameInput.value = userLastName;
        positionInput.value = userPosition;
        submitBtn.textContent = 'Редактировать';
        delBTns.forEach(function (btn) {
            btn.classList.remove('disabled');
        })
        tableRowsNodes[userId].querySelector('.delete').classList.add('disabled');
        if ( editForm.querySelector('input[value="add"]') ) {
            editForm.querySelector('input[value="add"]').remove();
        }
        if ( !editForm.querySelector('input[value="edit"]') ) {
            editForm.insertAdjacentHTML('afterbegin','<input type="hidden" name="action" value="edit">');
        }
        if ( !editForm.querySelector('input[name="user_id"]') ) {
            editForm.insertAdjacentHTML('afterbegin','<input type="hidden" name="user_id" value="' + userId + '">');
        }
        if ( editForm.querySelector('input[name="user_id"]') ) {
            editForm.querySelector('input[name="user_id"]').value = userId;
        }
    }
    if (action === 'add-user') {
        firstNameInput.value = '';
        lastNameInput.value = '';
        submitBtn.textContent = 'Добавить';
        delBTns.forEach(function (btn) {
            btn.classList.remove('disabled');
        })
        if ( editForm.querySelector('input[value="edit"]') ) {
            editForm.querySelector('input[value="edit"]').remove();
        }
        if ( editForm.querySelector('input[name="user_id"]') ) {
            editForm.querySelector('input[name="user_id"]').remove();
        }
        if ( !editForm.querySelector('input[value="add"]') ) {
            editForm.insertAdjacentHTML('afterbegin','<input type="hidden" name="action" value="add">');
        }
    }
    if ( action === 'delete' ) {
        let userId = tableRowsNodes.indexOf( e.target.parentElement );
        const data = {
            action : 'delete',
            user_id : userId
        }
        const response = await fetch(window.location.href + 'index.php',
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json;charset=utf-8'
                },
                body: JSON.stringify(data)
            });
        let result = await response.json();
        if ( result.deleted ) {
            tableRowsNodes[result.user_id].remove();
        }
    }
});

editForm.addEventListener('submit', async function (e) {
    e.preventDefault();
    firstNameInput.classList.remove('empty');
    lastNameInput.classList.remove('empty');
    const fd = new FormData(this);
    fd.append('position_name', positionInput.options[positionInput.selectedIndex].text);
    const response = await fetch(window.location.href + 'index.php', { method: 'POST', body: fd });
    let result = await response.json();
    if ( result.empty_field ) {
        document.querySelector('[name="' + result.empty_field + '"]').classList.add('empty');
    }
    if ( result.added ) {
        firstNameInput.value = '';
        lastNameInput.value = '';
        usersTable.insertAdjacentHTML('beforeend',
            '<tr>\n' +
            '                    <td class="first-name">' + result.first_name + '</td>\n' +
            '                    <td class="last-name">' + result.last_name + '</td>\n' +
            '                    <td class="position" data-value="' + result.position_val + '">' + result.position_name + '</td>\n' +
            '                    <td class="edit">Редактировать</td>\n' +
            '                    <td class="delete">Удалить</td>\n' +
            '                </tr>'
        );
    }
    if ( result.edited ) {
        let editedRow = tableRowsNodes[result.user_id];
        console.log(editedRow);
        editedRow.querySelector('.first-name').textContent = result.first_name;
        editedRow.querySelector('.last-name').textContent = result.last_name;
        editedRow.querySelector('.position').textContent = result.position_name;
        editedRow.querySelector('.position').dataset.value = result.position_val;
        firstNameInput.value = '';
        lastNameInput.value = '';
        editForm.querySelector('input[value="edit"]').remove();
        editForm.querySelector('input[name="user_id"]').remove();
        editForm.insertAdjacentHTML('afterbegin','<input type="hidden" name="action" value="add">');
        submitBtn.textContent = 'Добавить';
        delBTns.forEach(function (btn) {
            btn.classList.remove('disabled');
        })
    }
})