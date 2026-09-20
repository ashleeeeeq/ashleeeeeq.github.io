import Gantt from "frappe-gantt";
import Chart from "chart.js/auto";
import Choices from "choices.js";
import "./bulk-select.js";

const PASSWORD_VISIBLE_ICON =
    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
const PASSWORD_HIDDEN_ICON =
    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';

const createPasswordToggleButton = () => {
    const button = document.createElement("button");
    button.type = "button";
    button.className =
        "absolute right-4 top-1/2 -translate-y-1/2 text-primary1 transition-colors duration-200 hover:scale-110";
    button.setAttribute("aria-label", "Show password");
    button.dataset.passwordToggleButton = "true";
    button.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">${PASSWORD_HIDDEN_ICON}</svg>`;

    return button;
};

const syncPasswordToggleIcon = (button, isVisible) => {
    const svg = button.querySelector("svg");

    if (!svg) {
        return;
    }

    svg.innerHTML = isVisible ? PASSWORD_VISIBLE_ICON : PASSWORD_HIDDEN_ICON;
    button.setAttribute(
        "aria-label",
        isVisible ? "Hide password" : "Show password",
    );
};

const bindPasswordToggle = (input, button) => {
    if (!input || !button || button.dataset.passwordToggleBound === "true") {
        return;
    }

    button.dataset.passwordToggleBound = "true";
    syncPasswordToggleIcon(button, input.type === "text");

    button.addEventListener("click", () => {
        input.type = input.type === "password" ? "text" : "password";
        syncPasswordToggleIcon(button, input.type === "text");
    });
};

const enhancePasswordField = (input) => {
    if (
        !(input instanceof HTMLInputElement) ||
        input.type !== "password" ||
        input.dataset.passwordToggleEnhanced === "true"
    ) {
        return;
    }

    const existingButton = input.parentElement?.querySelector(
        'button[type="button"]',
    );

    if (existingButton) {
        input.classList.add("pr-12");
        input.dataset.passwordToggleEnhanced = "true";
        existingButton.className =
            "absolute right-4 top-1/2 -translate-y-1/2 text-primary1 transition-colors duration-200 hover:scale-110";
        bindPasswordToggle(input, existingButton);
        return;
    }

    const parent = input.parentElement;
    if (parent && getComputedStyle(parent).position === "relative") {
        input.classList.add("pr-12");
        input.dataset.passwordToggleEnhanced = "true";
        const button = createPasswordToggleButton();
        parent.appendChild(button);
        bindPasswordToggle(input, button);
        return;
    }

    const wrapper = document.createElement("div");
    wrapper.className = "relative";

    input.parentNode?.insertBefore(wrapper, input);
    wrapper.appendChild(input);

    input.classList.add("pr-12");
    input.dataset.passwordToggleEnhanced = "true";

    const button = createPasswordToggleButton();
    wrapper.appendChild(button);
    bindPasswordToggle(input, button);
};

const initializePasswordToggles = () => {
    document
        .querySelectorAll('input[type="password"]')
        .forEach(enhancePasswordField);
};

const PASSWORD_REQUIREMENTS = [
    { key: "length", label: "At least 12 characters", test: (v) => v.length >= 12 },
    { key: "uppercase", label: "Contains uppercase letter", test: (v) => /[A-Z]/.test(v) },
    { key: "lowercase", label: "Contains lowercase letter", test: (v) => /[a-z]/.test(v) },
    { key: "number", label: "Contains a number", test: (v) => /\d/.test(v) },
    { key: "symbol", label: "Contains a symbol", test: (v) => /[^a-zA-Z0-9]/.test(v) },
];

const applyItemStyle = (li, passed, passedClass, failedClass) => {
    if (passed) {
        if (passedClass) {
            li.classList.remove(failedClass);
            li.classList.add(passedClass);
            li.style.color = "";
        } else {
            li.style.color = "rgba(16,185,129,0.8)";
            li.className = "flex items-center gap-1.5 text-xs transition-all duration-150";
        }
    } else {
        if (failedClass) {
            li.classList.remove(passedClass);
            li.classList.add(failedClass);
            li.style.color = "";
        } else {
            li.style.color = "rgba(255,255,255,0.4)";
            li.className = "flex items-center gap-1.5 text-xs transition-all duration-150";
        }
    }
};

const initPasswordRequirements = (inputId, listId, opts = {}) => {
    const { passedClass, failedClass } = opts;
    const input = document.getElementById(inputId);
    const list = document.getElementById(listId);
    if (!input || !list) return;

    const items = PASSWORD_REQUIREMENTS.map((req) => {
        const li = document.createElement("li");
        li.className = "flex items-center gap-1.5 text-xs transition-all duration-150";
        li.dataset.requirement = req.key;
        li.textContent = req.label;
        list.appendChild(li);
        return li;
    });

    const update = () => {
        const value = input.value || "";
        items.forEach((li, i) => {
            const passed = PASSWORD_REQUIREMENTS[i].test(value);
            applyItemStyle(li, passed, passedClass, failedClass);
            li.textContent = (passed ? "✓ " : "○ ") + PASSWORD_REQUIREMENTS[i].label;
        });
    };

    input.addEventListener("input", update);
    update();
};

const initPasswordMatch = (inputId, confirmInputId, listId, opts = {}) => {
    const { passedClass, failedClass } = opts;
    const input = document.getElementById(inputId);
    const confirmInput = document.getElementById(confirmInputId);
    const list = document.getElementById(listId);
    if (!input || !confirmInput || !list) return;

    const li = document.createElement("li");
    li.className = "flex items-center gap-1.5 text-xs transition-all duration-150";
    list.appendChild(li);

    const update = () => {
        const passed = input.value && input.value === confirmInput.value;
        applyItemStyle(li, passed, passedClass, failedClass);
        li.textContent = (passed ? "✓ " : "○ ") + "Passwords match";
    };

    input.addEventListener("input", update);
    confirmInput.addEventListener("input", update);
    update();
};

window.initPasswordRequirements = initPasswordRequirements;
window.initPasswordMatch = initPasswordMatch;

const initializeDashboardPeriodFilters = () => {
    const forms = document.querySelectorAll("[data-dashboard-period-form]");

    forms.forEach((form) => {
        const periodSelect = form.querySelector("[data-period-select]");

        if (!(periodSelect instanceof HTMLSelectElement)) {
            return;
        }

        const applyPeriodVisibility = (period) => {
            form.querySelectorAll("[data-period-field]").forEach((field) => {
                const targetPeriod = field.getAttribute("data-period-field");
                const visible = targetPeriod === period;

                field.classList.toggle("hidden", !visible);

                field
                    .querySelectorAll("input, select, textarea")
                    .forEach((input) => {
                        input.disabled = !visible;
                    });
            });
        };

        applyPeriodVisibility(periodSelect.value || "month");
        periodSelect.addEventListener("change", (event) => {
            applyPeriodVisibility(event.target.value || "month");
        });
    });
};

// Expose Chart globally for PDF report charts (no conflict with dashboard)
window.Chart = Chart;
window.Choices = Choices;

const findOverflowParent = (el) => {
    let parent = el.parentElement;
    while (parent && parent !== document.body) {
        const overflow = getComputedStyle(parent).overflow;
        if (
            overflow === "hidden" ||
            overflow === "clip" ||
            overflow === "auto"
        ) {
            return parent;
        }
        parent = parent.parentElement;
    }
    return null;
};

const initializeChoices = () => {
    document.querySelectorAll("select[data-choices]").forEach((el) => {
        if (el.dataset.choicesInitialized) return;
        // Preserve any pre-captured snapshot (e.g., from allocations/create bulk filter)
        const hasExistingSnapshot = Array.isArray(el._originalOptions) && el._originalOptions.length > 0;
        const optionCount = el.options.length;
        const choices = new Choices(el, {
            searchEnabled: optionCount > 10,
            shouldSort: false,
            itemSelectText: '',
        });
        el._choicesInstance = choices;

        // Save original options only if not already captured (respect pre-filter snapshot)
        if (!hasExistingSnapshot) {
            el._originalOptions = Array.from(el.options).map((opt) => ({
                value: opt.value,
                label: opt.text,
                selected: opt.selected,
                disabled: opt.disabled,
                customProperties: {
                    ...opt.dataset,
                },
            }));
        }

        el.dataset.choicesInitialized = "true";

        let overflowParent = null;

        el.addEventListener("showDropdown", () => {
            if (!overflowParent) {
                overflowParent = findOverflowParent(el.closest(".choices"));
            }
            if (overflowParent) {
                overflowParent.dataset.choicesOverflow =
                    overflowParent.style.overflow;
                overflowParent.style.overflow = "visible";
            }
        });

        el.addEventListener("hideDropdown", () => {
            if (overflowParent) {
                const prev = overflowParent.dataset.choicesOverflow;
                overflowParent.style.overflow = prev || "";
                delete overflowParent.dataset.choicesOverflow;
                overflowParent = null;
            }
        });
    });
};

window.initializeChoices = initializeChoices;

if (document.readyState === "loading") {
    document.addEventListener(
        "DOMContentLoaded",
        () => {
            initializePasswordToggles();
            initializeDashboardPeriodFilters();
            initializeChoices();
        },
        { once: true },
    );
} else {
    initializePasswordToggles();
    initializeDashboardPeriodFilters();
    initializeChoices();
}

const deliverablesGantt = document.getElementById("deliverables-gantt-chart");
const deliverablesGanttData = document.getElementById(
    "deliverables-gantt-tasks",
);

const dashboardChartNodes = document.querySelectorAll("[data-dashboard-chart]");

var _gradCache = {};
var _extractRgb = function (str) {
    var m = str.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
    return m ? m[1] + "," + m[2] + "," + m[3] : null;
};

function rgbToHsl(r, g, b) {
    r /= 255;
    g /= 255;
    b /= 255;

    var max = Math.max(r, g, b);
    var min = Math.min(r, g, b);

    var h,
        s,
        l = (max + min) / 2;

    if (max === min) {
        h = s = 0;
    } else {
        var d = max - min;

        s = l > 0.5 ? d / (2 - max - min) : d / (max + min);

        switch (max) {
            case r:
                h = (g - b) / d + (g < b ? 6 : 0);
                break;
            case g:
                h = (b - r) / d + 2;
                break;
            case b:
                h = (r - g) / d + 4;
                break;
        }

        h /= 6;
    }

    return [h * 360, s, l];
}

function hslToRgb(h, s, l) {
    h /= 360;

    function hue2rgb(p, q, t) {
        if (t < 0) t += 1;
        if (t > 1) t -= 1;
        if (t < 1 / 6) return p + (q - p) * 6 * t;
        if (t < 1 / 2) return q;
        if (t < 2 / 3) return p + (q - p) * (2 / 3 - t) * 6;
        return p;
    }

    var r, g, b;

    if (s === 0) {
        r = g = b = l;
    } else {
        var q = l < 0.5 ? l * (1 + s) : l + s - l * s;
        var p = 2 * l - q;

        r = hue2rgb(p, q, h + 1 / 3);
        g = hue2rgb(p, q, h);
        b = hue2rgb(p, q, h - 1 / 3);
    }

    return [Math.round(r * 255), Math.round(g * 255), Math.round(b * 255)];
}

function nextWheelColor(rgbStr, degrees) {
    degrees = degrees || 45;

    var p = rgbStr.split(",").map(Number);

    var hsl = rgbToHsl(p[0], p[1], p[2]);

    hsl[0] = (hsl[0] + degrees) % 360;

    return hslToRgb(hsl[0], hsl[1], hsl[2]).join(",");
}

var applyGradientBackgrounds = function (config) {
    if (!config || !config.data || !config.data.datasets) return config;
    var type = config.type;
    config.data.datasets.forEach(function (ds) {
        var orig = ds.backgroundColor;

        if (type === "doughnut" || type === "pie") {
            if (Array.isArray(orig)) {
                var parsed = orig.map(function (c) {
                    var rgb = _extractRgb(c);
                    var m = c.match(/[\d.]+(?=\)$)/);
                    var a = m ? parseFloat(m[0]) : 0.88;

                    return {
                        rgb: rgb,
                        a: a,
                        orig: c,
                    };
                });

                ds.backgroundColor = function (ctx) {
                    var chart = ctx.chart;
                    var idx = ctx.dataIndex;

                    var item = parsed[idx] || parsed[0];

                    if (!item || !item.rgb) {
                        return item ? item.orig : "rgba(0,0,0,0.88)";
                    }

                    if (!chart.chartArea) {
                        return "rgba(" + item.rgb + "," + item.a + ")";
                    }

                    var nextRgb = nextWheelColor(item.rgb, 30);

                    var g = chart.ctx.createLinearGradient(
                        0,
                        chart.chartArea.top,
                        0,
                        chart.chartArea.bottom,
                    );

                    g.addColorStop(0, "rgba(" + item.rgb + ",.95)");
                    g.addColorStop(1, "rgba(" + nextRgb + ",.95)");

                    return g;
                };
            }

            return;
        }

        if (type !== "bar" && type !== "line") return;

        if (type === "bar") {
            if (typeof orig === "string") {
                var rgb = _extractRgb(orig);
                if (!rgb) return;
                ds.backgroundColor = function (ctx) {
                    var chart = ctx.chart;
                    if (!chart.chartArea) return "rgba(" + rgb + ",0.88)";
                    var g = chart.ctx.createLinearGradient(
                        0,
                        chart.chartArea.top,
                        0,
                        chart.chartArea.bottom,
                    );
                    g.addColorStop(0, "rgba(" + rgb + ",0.88)");
                    g.addColorStop(1, "rgba(" + rgb + ",0.30)");
                    return g;
                };
            } else if (Array.isArray(orig)) {
                ds.backgroundColor = function (ctx) {
                    var chart = ctx.chart;
                    var idx = ctx.dataIndex;
                    var c = orig[idx] || orig[0];
                    var rgb = _extractRgb(c);
                    if (!rgb || !chart.chartArea) return c;
                    var g = chart.ctx.createLinearGradient(
                        0,
                        chart.chartArea.top,
                        0,
                        chart.chartArea.bottom,
                    );
                    g.addColorStop(0, "rgba(" + rgb + ",0.88)");
                    g.addColorStop(1, "rgba(" + rgb + ",0.30)");
                    return g;
                };
            }
        } else if (type === "line") {
            if (typeof orig === "string") {
                var rgb = _extractRgb(orig);
                var nextRgb = nextWheelColor(rgb, 30);

                ds.backgroundColor = function (ctx) {
                    var chart = ctx.chart;
                    if (!chart.chartArea) return "rgba(" + rgb + ",0.12)";
                    var g = chart.ctx.createLinearGradient(
                        0,
                        chart.chartArea.top,
                        0,
                        chart.chartArea.bottom,
                    );
                    g.addColorStop(0, "rgba(" + rgb + ",0.25)");
                    g.addColorStop(1, "rgba(" + rgb + ",0.02)");
                    return g;
                };

                ds.borderColor = function (ctx) {
                    var chart = ctx.chart;
                    if (!chart.chartArea) return "rgba(" + rgb + ",1)";

                    var g = chart.ctx.createLinearGradient(
                        chart.chartArea.left,
                        0,
                        chart.chartArea.right,
                        0,
                    );

                    g.addColorStop(0, "rgba(" + rgb + ",1)");
                    g.addColorStop(0.75, "rgba(" + nextRgb + ",1)");
                    g.addColorStop(1, "rgba(" + nextRgb + ",1)");
                    return g;
                };
            }
        }
    });
    return config;
};

window.applyGradientBackgrounds = applyGradientBackgrounds;

const enhanceDashboardChartConfig = (config) => {
    const metric = config?.dashboardMetric;

    if (!metric || !config?.data?.datasets?.length) {
        return config;
    }

    const tooltipLabel = (context) => {
        const label = context.dataset?.label
            ? `${context.dataset.label}: `
            : "";
        const value =
            context.formattedValue ?? context.parsed?.y ?? context.parsed ?? "";

        return `${label}${value}%`;
    };

    const tooltipDetail = (context) => {
        const countInfo = context.dataset?.tooltipCounts?.[context.dataIndex];

        if (!countInfo) {
            return null;
        }

        if (metric === "transitionRate") {
            return `Success: ${countInfo.success ?? 0} / Total: ${countInfo.total ?? 0}`;
        }

        if (metric === "completionRate") {
            return `Completed: ${countInfo.completed ?? 0} / Total: ${countInfo.total ?? 0}`;
        }

        return null;
    };

    config.options ??= {};
    config.options.plugins ??= {};
    config.options.plugins.tooltip ??= {};
    config.options.plugins.tooltip.callbacks ??= {};

    const existingLabelCallback =
        config.options.plugins.tooltip.callbacks.label;

    config.options.plugins.tooltip.callbacks.label = (context) => {
        const baseLabel =
            typeof existingLabelCallback === "function"
                ? existingLabelCallback(context)
                : tooltipLabel(context);
        const detailLabel = tooltipDetail(context);

        return detailLabel ? [baseLabel, detailLabel] : baseLabel;
    };

    return config;
};

if (dashboardChartNodes.length) {
    Chart.defaults.color = "rgba(255, 255, 255, 0.72)";
    Chart.defaults.borderColor = "rgba(255, 255, 255, 0.1)";

    dashboardChartNodes.forEach((node) => {
        const configText = node.getAttribute("data-chart-config");

        if (!configText) {
            return;
        }

        try {
            const config = applyGradientBackgrounds(
                enhanceDashboardChartConfig(JSON.parse(configText)),
            );
            new Chart(node, config);
        } catch (error) {
            console.error("Failed to render dashboard chart", error);
        }
    });
}

if (deliverablesGantt && deliverablesGanttData) {
    try {
        const tasks = JSON.parse(deliverablesGanttData.textContent || "[]");
        const csrfToken =
            document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content") || "";
        const updateUrlBase = deliverablesGantt.dataset.updateUrlBase || "";
        const escapeHtml = (value) =>
            String(value)
                .replaceAll("&", "&amp;")
                .replaceAll("<", "&lt;")
                .replaceAll(">", "&gt;")
                .replaceAll('"', "&quot;")
                .replaceAll("'", "&#39;");
        const formatDateForApi = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, "0");
            const day = String(date.getDate()).padStart(2, "0");

            return `${year}-${month}-${day}`;
        };
        const formatDateForDisplay = (value) => {
            if (!value) {
                return "N/A";
            }

            const iso = /^\d{4}-\d{2}-\d{2}$/;
            const date = iso.test(value)
                ? new Date(`${value}T00:00:00`)
                : new Date(value);

            if (Number.isNaN(date.getTime())) {
                return String(value);
            }

            return new Intl.DateTimeFormat("en-US", {
                month: "long",
                day: "numeric",
                year: "numeric",
            }).format(date);
        };
        const formatStatusLabel = (task) => {
            const progress = Number(task.progress || 0);
            const end = task.end ? new Date(task.end) : null;
            const today = new Date();
            if (end) {
                end.setHours(0, 0, 0, 0);
            }
            today.setHours(0, 0, 0, 0);

            if (progress >= 100) return "Completed";
            if (end && end < today && progress < 100) return "Overdue";
            if (progress === 0) return "Not Started";
            return "In Progress";
        };
        const taskSaveVersion = new Map();
        const minVisibleRows = 10;
        const editModal = document.getElementById("deliverable-edit-modal");
        const editForm = document.getElementById("deliverable-edit-form");
        const editId = document.getElementById("deliverable-edit-id");
        const editTitle = document.getElementById("deliverable-edit-title");
        const editDescription = document.getElementById(
            "deliverable-edit-description",
        );
        const editStart = document.getElementById("deliverable-edit-start");
        const editEnd = document.getElementById("deliverable-edit-end");
        const editProgress = document.getElementById(
            "deliverable-edit-progress",
        );
        const editCancel = document.getElementById("deliverable-edit-cancel");
        let ganttTasks = tasks.map((task) => ({ ...task }));

        let ganttChart = null;

        const updateListingRow = (deliverable) => {
            const row = document.querySelector(
                `[data-deliverable-id="${deliverable.id}"]`,
            );

            if (!row) {
                return;
            }

            const startCell = row.querySelector('[data-field="start"]');
            const endCell = row.querySelector('[data-field="end"]');
            const statusCell = row.querySelector('[data-field="status"]');
            const titleCell = row.querySelector('[data-field="title"]');
            const descriptionCell = row.querySelector(
                '[data-field="description"]',
            );
            const progressCell = row.querySelector('[data-field="progress"]');

            if (titleCell) {
                titleCell.textContent =
                    deliverable.title || deliverable.name || "Untitled";
            }

            if (descriptionCell) {
                descriptionCell.textContent = deliverable.description || "";
            }

            if (startCell) {
                startCell.textContent = formatDateForDisplay(deliverable.start);
            }

            if (endCell) {
                endCell.textContent = formatDateForDisplay(deliverable.end);
            }

            if (statusCell) {
                const span = statusCell.querySelector("span");
                if (span) {
                    const label = formatStatusLabel(deliverable);
                    span.textContent = label;
                    if (label === "Completed") {
                        span.style.backgroundColor = "rgba(16,185,129,0.15)";
                        span.style.color = "#10b981";
                    } else if (label === "Overdue") {
                        span.style.backgroundColor = "rgba(248,113,113,0.15)";
                        span.style.color = "#f87171";
                    } else if (label === "Not Started") {
                        span.style.backgroundColor = "rgba(107,114,128,0.15)";
                        span.style.color = "#6b7280";
                    } else {
                        span.style.backgroundColor = "rgba(245,158,11,0.15)";
                        span.style.color = "#f59e0b";
                    }
                }
            }

            if (progressCell) {
                const pct = Math.round(Number(deliverable.progress || 0));
                const fillBar =
                    progressCell.querySelector(".progress-fill-bar");
                const pctText = progressCell.querySelector(".progress-pct");
                if (fillBar) fillBar.style.width = `${Math.min(pct, 100)}%`;
                if (pctText) pctText.textContent = `${pct}%`;
            }
        };

        const removeListingRow = (id) => {
            const row = document.querySelector(`[data-deliverable-id="${id}"]`);
            if (row) {
                row.remove();
            }
        };

        const findTaskById = (id) =>
            ganttTasks.find((task) => String(task.id) === String(id));

        const syncTaskInCollection = (nextTask) => {
            const index = ganttTasks.findIndex(
                (task) => String(task.id) === String(nextTask.id),
            );

            if (index >= 0) {
                ganttTasks[index] = { ...ganttTasks[index], ...nextTask };
            } else {
                ganttTasks.push({ ...nextTask });
            }

            return ganttTasks.find(
                (task) => String(task.id) === String(nextTask.id),
            );
        };

        const normalizeTaskStateClass = (taskId, nextClass) => {
            if (!ganttChart) {
                return;
            }

            const bar = ganttChart.get_bar(String(taskId));
            const wrapper = bar?.group;

            if (!wrapper) {
                return;
            }

            [...wrapper.classList]
                .filter((className) =>
                    className.startsWith("deliverable-state-"),
                )
                .forEach((className) => wrapper.classList.remove(className));

            if (nextClass) {
                wrapper.classList.add(nextClass);
            }
        };

        const deleteDeliverable = async (task) => {
            if (!updateUrlBase) {
                return;
            }

            const response = await fetch(`${updateUrlBase}/${task.id}`, {
                method: "DELETE",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            ganttTasks = ganttTasks.filter(
                (item) => String(item.id) !== String(task.id),
            );
            ganttChart.refresh(ganttTasks);
            removeListingRow(String(task.id));
        };

        const persistDeliverable = async (task, payload) => {
            if (!updateUrlBase) {
                return;
            }

            const currentVersion = (taskSaveVersion.get(task.id) || 0) + 1;
            taskSaveVersion.set(task.id, currentVersion);

            const requestBody = {
                title: payload.title || task.title || task.name,
                description: payload.description ?? task.description ?? null,
                grant_id: task.grant_id ?? task.donor_grant_id ?? null,
                start_date: payload.start_date || task.start,
                end_date: payload.end_date || task.end,
            };

            if (Object.prototype.hasOwnProperty.call(payload, "progress")) {
                requestBody.progress = Math.max(
                    0,
                    Math.min(100, Math.round(Number(payload.progress) || 0)),
                );
            }

            const response = await fetch(`${updateUrlBase}/${task.id}`, {
                method: "PUT",
                headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                },
                body: JSON.stringify(requestBody),
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            const data = await response.json();
            const deliverable = data.deliverable || {};

            if (taskSaveVersion.get(task.id) !== currentVersion) {
                return;
            }

            const nextTask = {
                ...task,
                id: String(deliverable.id || task.id),
                name: deliverable.name || task.name,
                title: deliverable.title || task.title || task.name,
                description:
                    deliverable.description ?? task.description ?? null,
                grant_id:
                    deliverable.grant_id ??
                    deliverable.donor_grant_id ??
                    task.grant_id ??
                    task.donor_grant_id ??
                    null,
                grant: deliverable.grant ?? task.grant ?? null,
                start: deliverable.start || payload.start_date || task.start,
                end: deliverable.end || payload.end_date || task.end,
                progress: Number.isFinite(Number(deliverable.progress))
                    ? Number(deliverable.progress)
                    : Number(task.progress || 0),
                derived_status:
                    deliverable.derived_status || task.derived_status,
                custom_class:
                    deliverable.custom_class ||
                    `deliverable-state-${String(deliverable.derived_status || task.derived_status || "not_started").replaceAll("_", "-")}`,
            };

            const syncedTask = syncTaskInCollection(nextTask);

            ganttChart.update_task(task.id, nextTask);
            normalizeTaskStateClass(nextTask.id, nextTask.custom_class);
            Object.assign(task, syncedTask);
            updateListingRow(syncedTask);

            return syncedTask;
        };

        const openEditModal = (task) => {
            if (!editModal || !editForm) {
                return;
            }

            editId.value = String(task.id);
            editTitle.value = task.title || task.name || "";
            editDescription.value = task.description || "";
            editStart.value = task.start || "";
            editEnd.value = task.end || "";
            editProgress.value = Math.max(
                0,
                Math.min(100, Math.round(Number(task.progress) || 0)),
            );
            editModal.showModal();
        };

        if (editCancel && editModal) {
            editCancel.addEventListener("click", () => {
                editModal.close();
            });
        }

        if (editForm && editModal) {
            editForm.addEventListener("submit", async (event) => {
                event.preventDefault();

                const task = findTaskById(editId.value);

                if (!task) {
                    editModal.close();
                    return;
                }

                const payload = {
                    title: editTitle.value.trim(),
                    description: editDescription.value.trim(),
                    start_date: editStart.value,
                    end_date: editEnd.value,
                    progress: Math.max(
                        0,
                        Math.min(
                            100,
                            Math.round(Number(editProgress.value) || 0),
                        ),
                    ),
                };

                try {
                    const savedTask = await persistDeliverable(task, payload);

                    if (savedTask) {
                        Object.assign(task, savedTask);
                    }

                    editModal.close();
                } catch (error) {
                    console.warn(
                        "Failed to save deliverable from modal",
                        error,
                    );
                    editModal.close();
                }
            });
        }

        if (ganttTasks.length) {
            const headerHeight = 52;
            const barHeight = 22;
            const rowPadding = 18;
            const rowHeight = barHeight + rowPadding;
            const minChartHeight =
                headerHeight + rowPadding + rowHeight * minVisibleRows - 10;

            ganttChart = new Gantt(deliverablesGantt, ganttTasks, {
                view_mode: "Week",
                date_format: "YYYY-MM-DD",
                header_height: headerHeight,
                bar_height: barHeight,
                padding: rowPadding,
                infinite_padding: false,
                scroll_to: "start",
                container_height: minChartHeight,
                readonly: !updateUrlBase,
                readonly_dates: !updateUrlBase,
                readonly_progress: !updateUrlBase,
                on_date_change: (task, newStartDate, newEndDate) => {
                    persistDeliverable(task, {
                        start_date: formatDateForApi(newStartDate),
                        end_date: formatDateForApi(newEndDate),
                    }).catch((error) => {
                        console.warn(
                            "Failed to save deliverable dates — reverting chart",
                            error,
                        );
                        if (ganttChart) {
                            ganttChart.refresh(ganttTasks);
                        }
                    });
                },
                on_progress_change: (task, progress) => {
                    persistDeliverable(task, {
                        progress: Math.max(
                            0,
                            Math.min(100, Math.round(Number(progress) || 0)),
                        ),
                    }).catch((error) => {
                        console.warn(
                            "Failed to save deliverable progress — reverting chart",
                            error,
                        );
                        if (ganttChart) {
                            ganttChart.refresh(ganttTasks);
                        }
                    });
                },
                popup: (ctx) => {
                    const { task } = ctx;
                    const grant = task.grant
                        ? `Grant: ${escapeHtml(task.grant)}<br/>`
                        : "";
                    const description = task.description
                        ? `${escapeHtml(task.description)}<br/>`
                        : "";
                    const status = formatStatusLabel(task);

                    ctx.set_title(escapeHtml(task.name));
                    ctx.set_subtitle(
                        `${escapeHtml(formatDateForDisplay(task.start))} to ${escapeHtml(formatDateForDisplay(task.end))}`,
                    );
                    ctx.set_details(
                        `${grant}${description}Status: ${escapeHtml(status)}<br/>Progress: ${escapeHtml(task.progress ?? 0)}%`,
                    );

                    if (updateUrlBase) {
                        const editIcon =
                            '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10"/></svg>';
                        const deleteIcon =
                            '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path/><path d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>';

                        ctx.add_action(
                            `<span title="Edit" aria-label="Edit" style="color: #white;">${editIcon}</span>`,
                            (taskArg) => {
                                openEditModal(taskArg);
                            },
                        );

                        ctx.add_action(
                            `<span title="Delete" aria-label="Delete" style="color:#f87171;">${deleteIcon}</span>`,
                            (taskArg) => {
                                const confirmed = window.confirm(
                                    "Delete this deliverable from the chart and listing?",
                                );
                                if (!confirmed) {
                                    return;
                                }

                                deleteDeliverable(taskArg).catch((error) => {
                                    console.warn(
                                        "Failed to delete deliverable",
                                        error,
                                    );
                                    if (ganttChart) {
                                        ganttChart.refresh(ganttTasks);
                                    }
                                });
                            },
                        );
                    }
                },
                popup_on: "click",
            });
        } else {
            deliverablesGantt.innerHTML =
                '<div class="rounded-xl border border-white/10 bg-white/5 px-4 py-6 text-sm text-white/55">No deliverables available for the gantt chart.</div>';
        }
    } catch (error) {
        deliverablesGantt.innerHTML =
            '<div class="rounded-xl border border-red-400/30 bg-red-500/10 px-4 py-6 text-sm text-red-200">Unable to render the gantt chart.</div>';
        console.error("Failed to initialize deliverables gantt", error);
    }
}

let activeSubmitButton = null;

document.addEventListener('click', (e) => {
    const button = e.target.closest(
        'button[type="submit"], input[type="submit"]'
    );

    if (button) {
        activeSubmitButton = button;
    }
});

document.addEventListener('submit', (e) => {
    if (!activeSubmitButton || activeSubmitButton.form !== e.target) return;

    activeSubmitButton.disabled = true;

    // Add disabled styling
    activeSubmitButton.classList.add('opacity-70', 'cursor-not-allowed');

    if (activeSubmitButton.tagName === 'BUTTON') {
        activeSubmitButton.innerHTML = 'Processing...';
    } else {
        activeSubmitButton.value = 'Processing...';
    }
});