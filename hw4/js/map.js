var centers = {
    基隆市: { center: [121.69, 25.11] },
    臺北市: { center: [121.56, 25.08] },
    新北市: { center: [121.64, 24.99] },
    桃園市: { center: [121.23, 24.86] },
    新竹市: { center: [120.95, 24.78] },
    新竹縣: { center: [121.17, 24.69] },
    苗栗縣: { center: [120.94, 24.51] },
    臺中市: { center: [120.95, 24.22] },
    彰化縣: { center: [120.45, 24.0] },
    南投縣: { center: [120.98, 23.84] },
    雲林縣: { center: [120.37, 23.66] },
    嘉義縣: { center: [120.54, 23.43] },
    嘉義市: { center: [120.45, 23.48] },
    臺南市: { center: [120.34, 23.15] },
    高雄市: { center: [120.58, 22.98] },
    屏東縣: { center: [120.63, 22.39] },
    臺東縣: { center: [121.18, 22.7] },
    花蓮縣: { center: [121.38, 23.74] },
    宜蘭縣: { center: [121.6, 24.55] },
    澎湖縣: { center: [119.5, 23.47] },
    金門縣: { center: [118.32, 24.45] },
    連江縣: { center: [120.21, 26.17] },
};

function getColor(rate, color) {
    if (rate >= 50) return d3.interpolateRgb('black', color)(0.4);
    else if (rate >= 45)
        return d3.interpolateRgb('black', color)(0.6);
    else if (rate >= 40)
        return d3.interpolateRgb('black', color)(0.7);
    else if (rate >= 35)
        return d3.interpolateRgb('black', color)(0.8);
    else if (rate >= 30)
        return d3.interpolateRgb('black', color)(0.9);
    else if (rate >= 25) return d3.interpolateRgb('black', color)(1);
    else if (rate >= 20) return d3.interpolateRgb('black', color)(1);
    else return d3.interpolateRgb('black', color)(1.2);
}

async function hightlight(county) {
    let mercatorScale,
        w = window.screen.width;

    switch (county) {
        case '新竹市':
        case '連江縣':
        case '金門縣':
        case '嘉義市':
        case '基隆市':
        case '臺北市':
            if (w > 1366) mercatorScale = 150000;
            else if (w <= 1366 && w > 480) mercatorScale = 120000;
            else mercatorScale = 100000;
            break;
        default:
            if (w > 1366) mercatorScale = 25000;
            else if (w <= 1366 && w > 480) mercatorScale = 20000;
            else mercatorScale = 10000;
            break;
    }
    var svg = d3.select('svg');
    var width = +svg.attr('width');
    var height = +svg.attr('height');

    // show back button
    d3.select('#go-back').style('display', 'flex');

    svg.selectAll('g').remove();
    const g = svg.append('g');

    var projectmethod = d3
        .geoMercator()
        .center(centers[county].center)
        .scale(mercatorScale)
        .translate([width / 2, height / 2]);
    var pathGenerator = d3.geoPath().projection(projectmethod);
    const jsondata = await Promise.all([
        d3.json('./json/taiwan-town.json'),
    ]);

    const geometries = topojson.feature(
        jsondata[0],
        jsondata[0].objects['TOWN_MOI_1080617'],
    );

    const data = await Promise.all(
        geometries.features
            .filter((d) => d.properties.COUNTYNAME === county)
            .map(
                async (d) =>
                    await get_data_for_chart(
                        county,
                        d.properties.TOWNNAME,
                    ),
            ),
    );

    const paths = g
        .selectAll('path')
        .data(
            geometries.features.filter(
                (d) => d.properties.COUNTYNAME === county,
            ),
        );
    paths
        .enter()
        .append('path')
        .attr('d', pathGenerator)
        .attr('class', 'town')
        .attr('fill', function (d, i) {
            let result = data[i];

            if (result === undefined) return 'white';

            let color = 'white';
            let rate = 0.0;
            if (
                result.candidate1_vote_rate >
                    result.candidate2_vote_rate &&
                result.candidate1_vote_rate >
                    result.candidate3_vote_rate
            ) {
                color = result.candidate1_color;
                rate = result.candidate1_vote_rate;
            } else if (
                result.candidate2_vote_rate >
                    result.candidate1_vote_rate &&
                result.candidate2_vote_rate >
                    result.candidate3_vote_rate
            ) {
                color = result.candidate2_color;
                rate = result.candidate2_vote_rate;
            } else {
                color = result.candidate3_color;
                rate = result.candidate3_vote_rate;
            }
            rate = Math.round(rate * 100);
            return getColor(rate, color);
        })
        .attr('stroke', 'white')
        .attr('stroke-width', 0.5)
        .on('mouseover', async function (d) {
            d3.select(this)
                .transition()
                .duration(300)
                .attr('transform', 'translate(0, -5)')
            d3.select('#county-name').text(
                d.srcElement.__data__.properties.COUNTYNAME +
                    d.srcElement.__data__.properties.TOWNNAME,
            );
            let result = await get_data_for_chart(
                county,
                d.srcElement.__data__.properties.TOWNNAME,
            );
            let pie = d3.pie().value(function (d) {
                if (d === 'candidate1')
                    return result.candidate1_vote_rate;
                else if (d === 'candidate2')
                    return result.candidate2_vote_rate;
                else return result.candidate3_vote_rate;
            });

            var info = d3.select('#county-info');
            info.style('display', 'block');
            const width = 224;
            let arc = d3
                .arc()
                .innerRadius(0)
                .outerRadius(width / 2);
            let arcs = pie([
                'candidate1',
                'candidate2',
                'candidate3',
            ]);

            d3.select('#county-info').selectAll('svg').remove();
            const svg = d3
                .select('#county-info')
                .append('svg')
                .attr('width', width)
                .attr('height', width)
                .append('g')
                .attr(
                    'transform',
                    'translate(' + width / 2 + ',' + width / 2 + ')',
                );

            svg.selectAll('path')
                .data(arcs)
                .enter()
                .append('path')
                .attr('d', arc)
                .attr('fill', function (d) {
                    if (d.data === 'candidate1')
                        return result.candidate1_color;
                    else if (d.data === 'candidate2')
                        return result.candidate2_color;
                    else return result.candidate3_color;
                });
            svg.selectAll('text')
                .data(arcs)
                .enter()
                .append('text')
                .attr('transform', function (d) {
                    return 'translate(' + arc.centroid(d) + ')';
                })
                .attr('dy', '.35em')
                .style('text-anchor', 'middle')
                .text(function (d) {
                    if (d.data === 'candidate1')
                        return result.candidate1_name;
                    else if (d.data === 'candidate2')
                        return result.candidate2_name;
                    else return result.candidate3_name;
                });
        })
        .on('mouseout', function () {
            d3.select(this)
                .transition()
                .duration(300)
                .attr('transform', 'translate(0, 0)')
            d3.select('#county-name').text('');
            d3.select('#county-info').selectAll('svg').remove();
            d3.select('#county-info').style('display', 'none');
        });
}

async function draw_map() {
    let mercatorScale,
        w = window.screen.width;
    if (w > 1366) mercatorScale = 9000;
    else if (w <= 1366 && w > 480) mercatorScale = 7000;
    else mercatorScale = 5000;

    var svg = d3.select('svg');
    var width = +svg.attr('width');
    var height = +svg.attr('height');
    const g = svg.append('g');

    var projectmethod = d3
        .geoMercator()
        .center([121, 24])
        .scale(mercatorScale)
        .translate([width / 2, height / 2.5]);
    var pathGenerator = d3.geoPath().projection(projectmethod);
    const jsondata = await Promise.all([
        d3.json('./json/taiwan-county.json'),
    ]);

    const geometries = topojson.feature(
        jsondata[0],
        jsondata[0].objects['COUNTY_MOI_1080617'],
    );

    // 先取得所有的資料
    const data = await Promise.all(
        geometries.features.map(
            async (d) =>
                await get_data_for_map(d.properties.COUNTYNAME),
        ),
    );

    const paths = g.selectAll('path').data(geometries.features);
    paths
        .enter()
        .append('path')
        .attr('d', pathGenerator)
        .attr('class', 'county')
        .attr('fill', function (d, i) {
            let result = data[i];
            let color = 'white';
            let rate = 0.0;
            // 選擇得票率最高的顏色
            if (
                result.candidate1_vote_rate >
                    result.candidate2_vote_rate &&
                result.candidate1_vote_rate >
                    result.candidate3_vote_rate
            ) {
                color = result.candidate1_color;
                rate = result.candidate1_vote_rate;
            } else if (
                result.candidate2_vote_rate >
                    result.candidate1_vote_rate &&
                result.candidate2_vote_rate >
                    result.candidate3_vote_rate
            ) {
                color = result.candidate2_color;
                rate = result.candidate2_vote_rate;
            } else {
                color = result.candidate3_color;
                rate = result.candidate3_vote_rate;
            }
            rate = Math.round(rate * 100);
            return getColor(rate, color);
        })
        .attr('stroke', 'white')
        .attr('stroke-width', 0.5)
        .on('mouseover', async function (d) {
            d3.select(this)
                .transition()
                .duration(300)
                .attr('transform', 'translate(0, -5)')
            // 顯示縣市名稱
            d3.select('#county-name').text(
                d.srcElement.__data__.properties.COUNTYNAME,
            );

            // 顯示得票率圓餅圖
            var info = d3.select('#county-info');
            info.style('display', 'block');
            let result = await get_data_for_map(
                d.srcElement.__data__.properties.COUNTYNAME,
            );
            let pie = d3.pie().value(function (d) {
                if (d === 'candidate1')
                    return result.candidate1_vote_rate;
                else if (d === 'candidate2')
                    return result.candidate2_vote_rate;
                else return result.candidate3_vote_rate;
            });

            const width = 224;
            let arc = d3
                .arc()
                .innerRadius(0)
                .outerRadius(width / 2);
            let arcs = pie([
                'candidate1',
                'candidate2',
                'candidate3',
            ]);

            d3.select('#county-info').selectAll('svg').remove();
            const svg = d3
                .select('#county-info')
                .append('svg')
                .attr('width', width)
                .attr('height', width)
                .append('g')
                .attr(
                    'transform',
                    'translate(' + width / 2 + ',' + width / 2 + ')',
                );

            svg.selectAll('path')
                .data(arcs)
                .enter()
                .append('path')
                .attr('d', arc)
                .attr('fill', function (d) {
                    if (d.data === 'candidate1')
                        return result.candidate1_color;
                    else if (d.data === 'candidate2')
                        return result.candidate2_color;
                    else return result.candidate3_color;
                });
            svg.selectAll('text')
                .data(arcs)
                .enter()
                .append('text')
                .attr('transform', function (d) {
                    return 'translate(' + arc.centroid(d) + ')';
                })
                .attr('dy', '.35em')
                .style('text-anchor', 'middle')
                .text(function (d) {
                    if (d.data === 'candidate1')
                        return result.candidate1_name;
                    else if (d.data === 'candidate2')
                        return result.candidate2_name;
                    else return result.candidate3_name;
                });
        })
        .on('mouseout', function () {
            d3.select(this)
                .transition()
                .duration(300)
                .attr('transform', 'translate(0, 0)')
            d3.select('#county-name').text('');
            d3.select('#county-info').selectAll('svg').remove();
            d3.select('#county-info').style('display', 'none');
        })
        .on('click', async function (d) {
            d3.select('#county-name').text('');
            d3.select('#county-info').selectAll('svg').remove();
            d3.select('#county-info').style('display', 'none');
            sessionStorage.setItem(
                'selectedCounty',
                d.srcElement.__data__.properties.COUNTYNAME,
            );
            await hightlight(
                d.srcElement.__data__.properties.COUNTYNAME,
            );
        });
}

draw_map();
d3.select('#year').on('change', async function () {
    console.log('change');
    d3.select('svg').selectAll('path').remove();
    if (sessionStorage.getItem('selectedCounty') === null) {
        draw_map();
    } else {
        await hightlight(sessionStorage.getItem('selectedCounty'));
    }
});
