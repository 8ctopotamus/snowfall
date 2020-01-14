;(function() {
  const { all_snowfall_data, current_city } = wp_data;
  const defaultColor = '#71b1f1';
  const currentCityColor = '#2e54b7';

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
    new Chart(ctx, {
      type: 'horizontalBar',
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
    const cityNames = state_snowfall_data.map(city => city['Name']);
    const dates = state_snowfall_data.map(city => city[`${idx} days DATE`]);
    const qty = state_snowfall_data.map(city => city[`${idx} days QTY`]);
    renderHorizBarChart(ctx, cityNames, dates, qty);
  };

  function renderGreatestChart() {
    const ctx = getCanvas('greatest-snowfall');
    const cityNames = state_snowfall_data.map(city => city['Name']);
    const dates = state_snowfall_data.map(city => city['GreatestEndingDate']);
    const qty = state_snowfall_data.map(city => city['Greatest Snowfall']);
    renderHorizBarChart(ctx, cityNames, dates, qty);
  };

  [1, 2, 3].forEach(idx => renderDayCharts(idx));

  renderGreatestChart();



  // tabs
  const tabs = Array.from(document.getElementsByClassName('tab'));
  const tabContent = Array.from(document.getElementsByClassName('tab-content'));

  function handleTabClick(e) {
    e.preventDefault();
    const targetId = this.getAttribute('href').substring(1);
    tabs.forEach(tab => tab.classList.remove('tab-active'));
    tabContent.forEach(tC => tC.classList.remove('tab-show'));
    this.classList.add('active');
    document.getElementById(targetId).classList.add('tab-show');
  };
  
  tabs.forEach(tab => tab.addEventListener('click', handleTabClick));

})();