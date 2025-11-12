document.addEventListener("DOMContentLoaded", () => {
  const styles = getComputedStyle(document.documentElement);
  const BRAND_PRIMARY =
    styles.getPropertyValue("--brand-primary").trim() || "#003a70";
  const BRAND_ACCENT =
    styles.getPropertyValue("--brand-accent").trim() || "#00a859";
  const GRID_COLOR = "rgba(0,0,0,0.06)";
  const elTotal = document.getElementById("totalFeedback");
  const elAvgClean = document.getElementById("avgCleanliness");
  const elAvgStaff = document.getElementById("avgStaff");
  const elAvgSec = document.getElementById("avgSecurity");
  const commentsTableBody = document.querySelector("#commentsTable tbody");
  const commentSearch = document.getElementById("commentSearch");

  const fromDate = document.getElementById("fromDate");
  const toDate = document.getElementById("toDate");
  const categorySelect = document.getElementById("categorySelect");
  const applyFiltersBtn = document.getElementById("applyFilters");
  const exportCsvLink = document.getElementById("exportCsv");

  let ratingsChartInstance = null;
  let dailyTrendInstance = null;

  function renderDistribution(distribution) {
    const ctx = document.getElementById("ratingsChart");
    if (!ctx) return;
    if (ratingsChartInstance) ratingsChartInstance.destroy();
    ratingsChartInstance = new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["1", "2", "3", "4", "5"],
        datasets: [
          {
            label: "Count",
            data: distribution,
            backgroundColor: BRAND_PRIMARY,
            borderRadius: 6,
          },
        ],
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true, precision: 0, grid: { color: GRID_COLOR } },
          x: { grid: { display: false } },
        },
      },
    });
  }

  function renderDailyTrend(labels, counts) {
    const ctx = document.getElementById("dailyTrendChart");
    if (!ctx) return;
    if (dailyTrendInstance) dailyTrendInstance.destroy();
    dailyTrendInstance = new Chart(ctx, {
      type: "line",
      data: {
        labels,
        datasets: [
          {
            label: "Feedback per Day",
            data: counts,
            borderColor: BRAND_ACCENT,
            backgroundColor: "rgba(0,168,89,0.15)",
            fill: true,
          },
        ],
      },
      options: {
        responsive: true,
        tension: 0.3,
        scales: {
          y: { beginAtZero: true, precision: 0, grid: { color: GRID_COLOR } },
          x: { grid: { display: false } },
        },
      },
    });
  }

  function setDefaultDates() {
    const today = new Date();
    const past = new Date();
    past.setDate(today.getDate() - 30);
    const toStr = today.toISOString().slice(0, 10);
    const fromStr = past.toISOString().slice(0, 10);
    if (fromDate && !fromDate.value) fromDate.value = fromStr;
    if (toDate && !toDate.value) toDate.value = toStr;
  }

  function buildQuery() {
    const params = new URLSearchParams();
    if (fromDate?.value) params.set("from", fromDate.value);
    if (toDate?.value) params.set("to", toDate.value);
    return params.toString();
  }

  function updateExportLink() {
    const q = buildQuery();
    exportCsvLink.href = "export_csv.php" + (q ? "?" + q : "");
  }

  function refresh() {
    const q = buildQuery();
    fetch("api_data.php" + (q ? "?" + q : ""))
      .then((res) => res.json())
      .then((data) => {
        if (!data) return;
        elTotal.textContent = data?.totals?.feedback ?? 0;
        elAvgClean.textContent = data?.averages?.cleanliness ?? 0;
        elAvgStaff.textContent = data?.averages?.staff ?? 0;
        elAvgSec.textContent = data?.averages?.security ?? 0;

        let dist = data?.distribution?.overall ?? [0, 0, 0, 0, 0];
        const cat = categorySelect?.value || "overall";
        if (cat !== "overall") {
          dist = data?.distribution?.[cat] ?? dist;
        }
        renderDistribution(dist);

        const labels = data?.daily?.labels ?? [];
        const counts = data?.daily?.count ?? [];
        renderDailyTrend(labels, counts);

        const comments = data?.comments ?? [];
        commentsTableBody.innerHTML = "";
        comments.forEach((c) => {
          const tr = document.createElement("tr");
          const tdComment = document.createElement("td");
          const tdTs = document.createElement("td");
          tdComment.textContent = c.comment;
          tdTs.textContent = c.timestamp;
          tr.appendChild(tdComment);
          tr.appendChild(tdTs);
          commentsTableBody.appendChild(tr);
        });

        if (commentSearch) {
          commentSearch.oninput = () => {
            const term = commentSearch.value.toLowerCase();
            [...commentsTableBody.querySelectorAll("tr")].forEach((tr) => {
              const text = tr.firstChild.textContent.toLowerCase();
              tr.style.display = text.includes(term) ? "" : "none";
            });
          };
        }

        updateExportLink();
      })
      .catch((err) => console.error("Failed to fetch dashboard data:", err));
  }

  if (applyFiltersBtn) applyFiltersBtn.addEventListener("click", refresh);
  if (categorySelect) categorySelect.addEventListener("change", refresh);

  setDefaultDates();
  refresh();
});
