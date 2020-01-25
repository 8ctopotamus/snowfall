;(function() {
  const { all_snowfall_data, current_city, site_url } = wp_data;
  const defaultColor = '#71b1f1';
  const currentCityColor = '#2e54b7';
  const tabs = Array.from(document.getElementsByClassName('tab'));
  const tabContent = Array.from(document.getElementsByClassName('tab-content'));

  const state_snowfall_data = all_snowfall_data
    .filter(city => city.STATE === current_city.STATE[0])

  function getCanvas(id) {
    const canvas = document.getElementById(id);
    // canvas.height = (state_snowfall_data.length * 10);
    ctx = canvas.getContext('2d');
    return ctx;
  };

  function determineBarColors() {
    return state_snowfall_data.map(city => current_city.Name[0] === city.Name ? currentCityColor : defaultColor);
  };

  function renderHorizBarChart(ctx, cityNames, dates, qty) {
    return new Chart(ctx, {
      type: 'horizontalBar',
      data: {
        labels: cityNames,
        datasets: [{
          label: 'Inches',
          data: qty,
          backgroundColor: determineBarColors(),
        }],
      },
      options: {
        tooltips: {
          callbacks: {
            footer: function(tooltipItem) {
              return dates[tooltipItem[0].index];
            }
          }
        },
        scales: {
          xAxes: [{
           display: true,
           position: 'top',
          }]
        },
      }
    });
  };

  function attachChartListeners(canvasId, chart) {
    document.getElementById(canvasId).onclick = function(evt) {
      var activePoints = chart.getElementsAtEvent(evt);
      if (activePoints.length > 0) {
        const label = activePoints[0]._model.label;
        const city = label.split(' - ')[1].replace(' ', '-').toLowerCase();
        const state = current_city['STATE'][0].toLowerCase();
        const url = `${site_url}/snowfall_records/${city}-${state}`;
        window.location = url;
      }
    };
  }

  function renderDayChart(idx) {
    const id = `most-snow-${idx}-days`
    const ctx = getCanvas(id);
    const cityNames = state_snowfall_data.map(city => `${city['Name']} - ${city['City']}`);
    const dates = state_snowfall_data.map(city => city[`${idx} days DATE`]);
    const qty = state_snowfall_data.map(city => city[`${idx} days QTY`]);
    const newChart = renderHorizBarChart(ctx, cityNames, dates, qty);
    attachChartListeners(id, newChart);
  };

  function renderGreatestChart() {
    const id = 'greatest-snowfall'
    const ctx = getCanvas(id);
    const cityNames = state_snowfall_data.map(city => `${city['Name']} - ${city['City']}`);
    const dates = state_snowfall_data.map(city => city['GreatestEndingDate']);
    const qty = state_snowfall_data.map(city => city['Greatest Snowfall']);
    const newChart = renderHorizBarChart(ctx, cityNames, dates, qty);
    attachChartListeners(id, newChart);
  };

  function handleTabClick(e) {
    e.preventDefault();
    tabs.forEach(tab => tab.classList.remove('tab-active'));
    tabContent.forEach(tC => tC.classList.remove('tab-show'));
    const targetId = this.getAttribute('href').substring(1);
    const idx = this.dataset.idx
    this.classList.add('tab-active');
    document.getElementById(targetId).classList.add('tab-show');
    renderDayChart(idx);
  };
  
  tabs.forEach(tab => tab.addEventListener('click', handleTabClick));

  renderDayChart(1);
  renderGreatestChart();

})();