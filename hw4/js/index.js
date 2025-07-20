const url = 'http://wwweb2024.csie.io:52000/api.php';
const Token = 'h6kcdm9pazx7j9xd';
const headers = {
    'Content-Type': 'application/json',
    Authorization: `Token ${Token}`,
};

async function get_data_for_taiwan() {
    let year = document.getElementById('year').value;
    const body = {
        area: '臺灣',
        year: year,
    };

    const response = await fetch(url, {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(body),
    });

    const data = await response.json();
    return data;
}

async function get_data_for_specific_county(county) {
    let year = document.getElementById('year').value;

    const body = {
        area: county,
        year: year,
    };

    const response = await fetch(url, {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(body),
    });

    const data = await response.json();
    return data;
}

async function get_data_for_map(county) {
    let data = await get_data_for_taiwan();

    for (let i = 0; i < data.length; i++) {
        if (data[i].county_name === county) return data[i];
    }
}

async function get_data_for_chart(county, town) {
    let data = await get_data_for_specific_county(county);

    for (let i = 0; i < data.length; i++) {
        if (data[i].township_name === town) return data[i];
    }
}

// 當選擇的年份改變時，將新的年份保存到 sessionStorage
document
    .getElementById('year')
    .addEventListener('change', function () {
        sessionStorage.setItem('selectedYear', this.value);
    });

// 當頁面載入時，從 sessionStorage 獲取保存的年份，並設定為當前選擇的年份
window.addEventListener('load', function () {
    const selectedYear = sessionStorage.getItem('selectedYear');
    if (selectedYear) {
        document.getElementById('year').value = selectedYear;
    }
});
document
    .getElementById('back')
    .addEventListener('click', function () {
        sessionStorage.removeItem('selectedCounty');
    });
