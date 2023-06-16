document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();

    var fileInput = document.getElementById('csvFile');
    var file = fileInput.files[0];
    var formData = new FormData();

    formData.append('csvFile', file);

    var authToken = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjIsImVtYWlsIjoiaXZhbkBnbWFpbC5jb20iLCJleHBpcmF0aW9uIjoxNjg3MTcwNTkwfQ.emraNSJqvcQLgTjtSpwce3O3OXZlmEAeUs-BMk1iwBo';
        //'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VySWQiOjEsImVtYWlsIjoic3RlZmFudmVsZXYzNDlAZ21haWwuY29tIiwiZXhwaXJhdGlvbiI6MTY4NzExNzE5Nn0.cQ_bWnGWJFq6J7uemPVGpMRNq2jFhjxl6We7d22TNdU';

    fetch('./../libs/readCSV.php', {
        method: 'POST',
        headers: {
            'Authorization': 'Bearer ' + authToken
        },
        body: formData
    })
        .then(response => response.text())
        .then(result => {
            // Process the response from the server
            console.log(result);
        })
        .catch(error => {
            // Handle any errors
            console.error(error);
        });
});