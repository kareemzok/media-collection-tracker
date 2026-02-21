// Main JavaScript for MediaTracker

document.addEventListener("DOMContentLoaded", () => {
  console.log("MediaTracker Initialized");

  // Subtle hover effect for glass cards
  const cards = document.querySelectorAll(".media-card");
  cards.forEach((card) => {
    card.addEventListener("mouseenter", () => {
      // Additional interactive logic if needed
    });
  });

  // Simple search highlighting
  const searchForm = document.querySelector('form[method="GET"]');
  if (searchForm) {
    const searchInput = searchForm.querySelector('input[name="search"]');
    if (searchInput && searchInput.value) {
      const searchTerm = searchInput.value.toLowerCase();
      const targets = document.querySelectorAll(
        ".media-card h3, .media-card p",
      );

      targets.forEach((target) => {
        const text = target.textContent;
        if (text.toLowerCase().includes(searchTerm)) {
          // highlighting logic could go here
        }
      });
    }
  }

  // AI Recommendations Fetching
  const aiBtn = document.getElementById("getAiRecommendations");
  if (aiBtn) {
    aiBtn.addEventListener("click", () => {
      const resultsContainer = document.getElementById(
        "aiRecommendationResults",
      );
      const btnText = document.getElementById("btnText");
      const statusMsg = document.getElementById("aiStatusMessage");

      // Reset and Show Loading State
      aiBtn.disabled = true;
      const originalText = btnText.textContent;
      btnText.innerHTML = '<span class="loading-spinner"></span> Thinking...';
      statusMsg.style.display = "block";
      statusMsg.textContent = "Analyzing your collection...";
      resultsContainer.style.display = "none";
      resultsContainer.innerHTML = "";

      fetch("api/get-recommendations.php")
        .then((response) => response.json())
        .then((data) => {
          // Update usage UI if provided
          if (data.usage_left !== undefined) {
            const usageCountEl = document.getElementById("aiRemainingCount");
            if (usageCountEl) {
              usageCountEl.textContent = data.usage_left;
            }
            if (data.usage_left <= 0) {
              aiBtn.disabled = true;
              aiBtn.classList.remove("btn-primary");
              aiBtn.classList.add("btn-glass");
              btnText.textContent = "Daily Limit Reached";
            } else {
              aiBtn.disabled = false;
              btnText.textContent = originalText;
            }
          } else {
            aiBtn.disabled = false;
            btnText.textContent = originalText;
          }

          if (data.error) {
            statusMsg.style.color = "#ef4444";
            statusMsg.textContent = "Error: " + data.error;
            // If the error is about daily limit, we might have already updated the UI above,
            // but let's make sure the button stays disabled.
            if (data.error.includes("daily limit")) {
              aiBtn.disabled = true;
              aiBtn.classList.remove("btn-primary");
              aiBtn.classList.add("btn-glass");
              btnText.textContent = "Daily Limit Reached";
            }
          } else {
            statusMsg.style.display = "none";
            resultsContainer.style.display = "block";

            // The data structure has changed to { recommendations: [], usage_left: X, ... }
            let recommendations = data.recommendations || [];

            // Further robustness: if recommendations is still not an array (e.g. somehow nested)
            if (!Array.isArray(recommendations)) {
              recommendations = [];
            }

            if (recommendations.length === 0) {
              resultsContainer.innerHTML =
                '<p style="color: var(--text-dim);">No recommendations found.</p>';
              return;
            }

            recommendations.forEach((rec) => {
              const item = document.createElement("div");
              item.className = "recommendation-item";
              item.innerHTML = `
                <span class="recommendation-type">${rec.type}</span>
                <div class="recommendation-title">${rec.title}</div>
                <div class="recommendation-reason">${rec.reason}</div>
              `;
              resultsContainer.appendChild(item);
            });
          }
        })
        .catch((err) => {
          aiBtn.disabled = false;
          btnText.textContent = originalText;
          statusMsg.style.color = "#ef4444";
          statusMsg.textContent =
            "Network error: Could not fetch recommendations.";
          console.error(err);
        });
    });
  }
});

function copyShareUrl() {
  const copyText = document.getElementById("shareUrl");
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(copyText.value);

  const notice = document.getElementById("copyNotice");
  if (notice) {
    notice.style.display = "block";
    setTimeout(() => {
      notice.style.display = "none";
    }, 3000);
  }
}
