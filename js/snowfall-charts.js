;(function() {
  const { all_snowfall_data, current_city } = wp_data;
  const defaultColor = '#71b1f1';
  const currentCityColor = '#2e54b7';

  function getCanvas(id) {
    const canvas = document.getElementById(id);
    canvas.height = (all_snowfall_data.length * 10);
    ctx = canvas.getContext('2d');
    return ctx;
  };

  function determineBarColors() {
    return all_snowfall_data.map(city => current_city.Name[0] === city.Name ? currentCityColor : defaultColor);
  };

  function renderHorizBarChart(ctx, cityNames, dates, qty) {
    new Chart(ctx, {
      type: 'horizontalBar',
      backgroundColor: 'red',
      data: {
        labels: cityNames,
        datasets: [{
          label: 'Inches',
          data: qty,
          backgroundColor: determineBarColors(),
        }]
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
        }
      }
    });
  };

  function renderDayCharts(idx) {
    const ctx = getCanvas(`most-snow-${idx}-days`);
    const cityNames = all_snowfall_data.map(city => city['Name']);
    const dates = all_snowfall_data.map(city => city[`${idx} days DATE`]);
    const qty = all_snowfall_data.map(city => city[`${idx} days QTY`]);
    renderHorizBarChart(ctx, cityNames, dates, qty);
  };

  function renderGreatestChart() {
    const ctx = getCanvas('greatest-snowfall');
    const cityNames = all_snowfall_data.map(city => city['Name']);
    const dates = all_snowfall_data.map(city => city['GreatestEndingDate']);
    const qty = all_snowfall_data.map(city => city['Greatest Snowfall']);
    renderHorizBarChart(ctx, cityNames, dates, qty);
  };

  [1, 2, 3].forEach(idx => renderDayCharts(idx));

  renderGreatestChart();
})();