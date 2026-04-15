// ================================================
// Event Scheduling System - Complete script.js
// FULLY WIRED: All tabs connected to real DB APIs
// ================================================

const state = {
    role: window.currentUserRole || "admin",
    tab: "dashboard",
    editingEventId: null
};

const el = (id) => document.getElementById(id);
const rolePill     = el("rolePill");
const viewTitle    = el("viewTitle");
const viewSubtitle = el("viewSubtitle");
const getMain = () => document.getElementById("mainContent");
const toast        = el("toast");
const toastTitle   = el("toastTitle");
const toastMsg     = el("toastMsg");

let allEventsCache  = [];
let dashboardEvents = [];

// ==================== TOAST ====================
function showToast(title, msg) {
    toastTitle.textContent = title;
    toastMsg.textContent   = msg;
    toast.classList.add("show");
    setTimeout(() => toast.classList.remove("show"), 2600);
}

function roleLabel(role) {
    if (role === "admin")     return "Admin";
    if (role === "organiser") return "Organizer";
    if (role === "requester") return "Attendee";
    return "User";
}

// ==================== HEADER ====================
function setHeaderForRole() {
    rolePill.textContent = roleLabel(state.role);
    if (state.role === "admin") {
        viewTitle.textContent    = "Admin Dashboard";
        viewSubtitle.textContent = "Manage venues, resources, time slots, and resolve conflicts.";
    } else if (state.role === "organiser") {
        viewTitle.textContent    = "Organizer Workspace";
        viewSubtitle.textContent = "Create events, request resources, and prevent scheduling conflicts.";
    } else {
        viewTitle.textContent    = "Attendee Portal";
        viewSubtitle.textContent = "Browse events and register safely.";
    }
}

function setActiveTab(tabKey) {
    state.tab = tabKey;
    document.querySelectorAll(".tab").forEach(t => {
        t.classList.toggle("active", t.dataset.tab === tabKey);
    });
    renderMain();
}

// ==================== FETCH ====================
async function fetchJSON(url, options = {}) {
    try {
        const res = await fetch(url, options);
        if (!res.ok) throw new Error("Network error " + res.status);
        return await res.json();
    } catch (err) {
        console.error("Fetch error:", err);
        return { success: false, error: err.message };
    }
}

// ==================== CONFLICT DETECTION ====================
function getConflictingEventIDs(events) {
    // Deduplicate by EventID first — same event appearing twice is NOT a conflict
    const seen = new Set();
    const unique = events.filter(ev => {
        if (seen.has(ev.EventID)) return false;
        seen.add(ev.EventID);
        return true;
    });

    const conflictIDs = new Set();
    for (let i = 0; i < unique.length; i++) {
        for (let j = i + 1; j < unique.length; j++) {
            const a = unique[i], b = unique[j];

            // Must be different events at the same venue on the same date
            if (a.EventID  === b.EventID)  continue;
            if (String(a.VenueID) !== String(b.VenueID)) continue;
            if (!a.VenueID || !b.VenueID)  continue;
            if (a.EventDate !== b.EventDate) continue;
            if (!a.EventDate || !b.EventDate) continue;

            const aParts = (a.TimeSlot || "").split("–");
            const bParts = (b.TimeSlot || "").split("–");
            if (aParts.length < 2 || bParts.length < 2) continue;

            const aStart = aParts[0].trim().substring(0, 5);
            const aEnd   = aParts[1].trim().substring(0, 5);
            const bStart = bParts[0].trim().substring(0, 5);
            const bEnd   = bParts[1].trim().substring(0, 5);

            if (aStart < bEnd && aEnd > bStart) {
                conflictIDs.add(String(a.EventID));
                conflictIDs.add(String(b.EventID));
            }
        }
    }
    return conflictIDs;
}

// ==================== SIDEBAR BADGE ====================
function updateSidebarBadge(kpi) {
    const badge = document.getElementById("sidebarBadge");
    if (!badge) return;

    if (kpi.role === "organiser") {
        const count = kpi.activeEvents || 0;
        badge.textContent = count === 1 ? "1 Event Active" : `${count} Events Active`;
        badge.className   = "badge";
        badge.style.background  = "rgba(78,205,196,0.18)";
        badge.style.color       = "#4ecdc4";
        badge.style.border      = "1px solid rgba(78,205,196,0.4)";
    } else if (kpi.role === "requester") {
        const count = kpi.openDays || 0;   // openDays = My Upcoming Events for requester
        badge.textContent = count === 1 ? "1 Upcoming" : `${count} Upcoming`;
        badge.className   = "badge";
        badge.style.background  = "rgba(100,200,255,0.15)";
        badge.style.color       = "#64c8ff";
        badge.style.border      = "1px solid rgba(100,200,255,0.35)";
    }
    // Admin keeps DB Connected — set by PHP, no JS update needed
}

// ==================== PENDING REQUESTS (organiser dashboard) ====================
async function loadPendingRequestsPanel() {
    const panel = document.getElementById("pendingRequestsPanel");
    if (!panel) return;

    // Fetch only PENDING bookings for this organiser's events
    const data = await fetchJSON("api/attendees.php?status=PENDING");
    if (!data.success) {
        panel.innerHTML = `<div style="color:#fb7185;font-size:13px;padding:8px 0;">Failed to load requests.</div>`;
        return;
    }

    const pending = (data.bookings || []).filter(b => b.BookingStatus === "PENDING");

    if (pending.length === 0) {
        panel.innerHTML = `<div style="color:var(--muted);font-size:13px;padding:12px 0;text-align:center;">
            ✅ No pending requests — all caught up!
        </div>`;
        return;
    }

    panel.innerHTML = pending.map(b => `
        <div style="
            display:flex;align-items:center;justify-content:space-between;
            padding:10px 0;border-bottom:1px solid var(--glass-border);
            gap:12px;flex-wrap:wrap;
        ">
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;font-weight:500;">${b.AttendeeName || "—"}</div>
                <div style="font-size:11.5px;color:var(--muted);margin-top:2px;">
                    ${b.EventName || "—"}
                    ${b.EventDate ? `· ${b.EventDate}` : ""}
                </div>
            </div>
            <div style="display:flex;gap:8px;flex-shrink:0;">
                <button class="btn small primary"
                    onclick="updateBookingStatus(${b.BookingID}, 'APPROVED', this)"
                    style="font-size:11.5px;padding:5px 14px;">
                    ✅ Approve
                </button>
                <button class="btn small danger"
                    onclick="updateBookingStatus(${b.BookingID}, 'REJECTED', this)"
                    style="font-size:11.5px;padding:5px 14px;">
                    ❌ Decline
                </button>
            </div>
        </div>
    `).join("");
}

window.updateBookingStatus = async function(bookingID, status, btn) {
    // Disable both buttons immediately to prevent double-click
    const row = btn.closest("div[style*='border-bottom']");
    if (row) row.querySelectorAll("button").forEach(b => b.disabled = true);

    const result = await fetchJSON("api/update-booking.php", {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify({ bookingID, status })
    });

    if (result.success) {
        showToast(status === "APPROVED" ? "Approved ✅" : "Declined ❌", result.message);

        // Fade the processed row out visually
        if (row) row.style.opacity = "0.3";

        setTimeout(() => {
            // 1. Refresh the organiser's own panels
            loadPendingRequestsPanel();
            loadGuestListPanel();

            // 2. Refresh the KPI tiles and event capacity counts
            //    so both organiser and admin see correct numbers
            Promise.all([
                fetchJSON("api/events-list.php"),
                fetchJSON("api/kpi-stats.php")
            ]).then(([eventsData, kpiData]) => {
                // Update the cached event list used by dashboard + filters
                if (eventsData.success && eventsData.events) {
                    dashboardEvents = eventsData.events;
                    allEventsCache  = eventsData.events;
                }

                // If admin is currently on the Attendees tab, refresh it live
                if (state.tab === "attendees") {
                    renderAttendeesTab(
                        el("attSearch")?.value.trim()  || "",
                        el("attEventSel")?.value       || "",
                        el("attStatus")?.value         || ""
                    );
                }

                // If admin is on the Events tab, refresh the event list
                if (state.tab === "events") {
                    renderEventsTab(dashboardEvents);
                }
            });

        }, 600);

    } else {
        showToast("Error", result.error || "Could not update booking.");
        if (row) row.querySelectorAll("button").forEach(b => b.disabled = false);
    }
};

// ==================== GUEST LIST (organiser dashboard) ====================
async function loadGuestListPanel() {
    const panel = document.getElementById("guestListPanel");
    if (!panel) return;

    const data = await fetchJSON("api/attendees.php");
    if (!data.success || !data.bookings || data.bookings.length === 0) {
        panel.innerHTML = `<div style="color:var(--muted);font-size:13px;padding:12px 0;text-align:center;">
            No registrations yet for your events.
        </div>`;
        return;
    }

    // Group bookings by event name, preserving insertion order
    const grouped = {};
    data.bookings.forEach(b => {
        const key = b.EventName || "Unknown Event";
        if (!grouped[key]) grouped[key] = [];
        grouped[key].push(b);
    });

    panel.innerHTML = Object.entries(grouped).map(([eventName, guests]) => {
        const approved = guests.filter(g => g.BookingStatus === "APPROVED").length;
        const pending  = guests.filter(g => g.BookingStatus === "PENDING").length;
        const total    = guests.length;

        // Guest rows — name, status, and remove button
        const rows = guests.map((g, i) => {
            const cls     = g.BookingStatus === "APPROVED" ? "ok"
                          : g.BookingStatus === "PENDING"  ? "warn"
                          : "danger";
            const isLast  = i === guests.length - 1;
            const canRemove = g.BookingStatus !== "CANCELLED" && g.BookingStatus !== "REJECTED";
            return `<div data-booking-row="${g.BookingID}" style="
                display:flex;
                align-items:center;
                justify-content:space-between;
                padding:7px 10px;
                gap:8px;
                ${!isLast ? "border-bottom:1px solid var(--glass-border);" : ""}
                font-size:12.5px;
                transition:opacity .3s;
            ">
                <span style="color:var(--white);flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    ${g.AttendeeName || "—"}
                </span>
                <span class="badge ${cls}" style="font-size:11px;padding:3px 8px;flex-shrink:0;">
                    ${g.BookingStatus}
                </span>
                ${canRemove ? `
                <button
                    onclick="removeGuest(${g.BookingID}, '${(g.AttendeeName || '').replace(/'/g,'')}')"
                    title="Remove ${g.AttendeeName} from this event"
                    style="
                        flex-shrink:0;
                        background:transparent;
                        border:1px solid rgba(251,113,133,0.4);
                        color:#fb7185;
                        border-radius:6px;
                        padding:3px 8px;
                        font-size:11px;
                        cursor:pointer;
                        transition:background .15s,color .15s;
                    "
                    onmouseover="this.style.background='rgba(251,113,133,0.15)'"
                    onmouseout="this.style.background='transparent'"
                >✕ Remove</button>` : `<span style="width:60px;display:inline-block;"></span>`}
            </div>`;
        }).join("");

        return `<div style="margin-bottom:14px;border-radius:12px;overflow:hidden;border:1px solid var(--glass-border);">
            <!-- Event header -->
            <div style="
                background:var(--glass);
                padding:8px 10px;
                display:flex;
                align-items:center;
                justify-content:space-between;
                border-bottom:1px solid var(--glass-border);
            ">
                <b style="font-size:12.5px;color:var(--white);">${eventName}</b>
                <span style="font-size:11px;color:var(--muted);">${total} guest${total !== 1 ? "s" : ""} &nbsp;·&nbsp; ✅${approved} ⏳${pending}</span>
            </div>
            <!-- Guest rows -->
            ${rows}
        </div>`;
    }).join("");
}

// Remove a guest from an event (sets booking to CANCELLED)
window.removeGuest = async function(bookingID, guestName) {
    if (!confirm(`Remove ${guestName} from this event? Their registration will be cancelled.`)) return;

    // Visually fade the row immediately
    const row = document.querySelector(`[data-booking-row="${bookingID}"]`);
    if (row) {
        row.style.opacity = "0.3";
        row.querySelectorAll("button").forEach(b => b.disabled = true);
    }

    const result = await fetchJSON("api/remove-guest.php", {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify({ bookingID })
    });

    if (result.success) {
        showToast("Removed", `${guestName} has been removed from the event.`);
        // Refresh panels and event data
        setTimeout(() => {
            loadGuestListPanel();
            loadPendingRequestsPanel();
            // Refresh event capacity counts
            Promise.all([
                fetchJSON("api/events-list.php"),
                fetchJSON("api/kpi-stats.php")
            ]).then(([eventsData, kpiData]) => {
                if (eventsData.success && eventsData.events) {
                    dashboardEvents = eventsData.events;
                    allEventsCache  = eventsData.events;
                }
                if (state.tab === "attendees") {
                    renderAttendeesTab(
                        el("attSearch")?.value.trim() || "",
                        el("attEventSel")?.value      || "",
                        el("attStatus")?.value         || ""
                    );
                }
                if (state.tab === "events") {
                    renderEventsTab(dashboardEvents);
                }
            });
        }, 400);
    } else {
        showToast("Error", result.error || "Could not remove guest.");
        if (row) {
            row.style.opacity = "1";
            row.querySelectorAll("button").forEach(b => b.disabled = false);
        }
    }
};

// ==================== DASHBOARD ====================
async function loadDashboardData() {
    const mc = getMain();
    if (mc) mc.innerHTML = `<div style="text-align:center;padding:60px;color:var(--muted);">
        <div style="font-size:2rem;margin-bottom:12px;">⏳</div>
        <div>Loading dashboard...</div>
    </div>`;

    try {
        const [eventsData, kpiData] = await Promise.all([
            fetchJSON("api/events-list.php"),
            fetchJSON("api/kpi-stats.php")
        ]);

        if (!eventsData.success) {
            const mc2 = getMain();
            if (mc2) mc2.innerHTML = `<div style="text-align:center;padding:60px;color:#fb7185;">
                <div style="font-size:2rem;margin-bottom:12px;">⚠️</div>
                <div>Dashboard failed to load.</div>
                <div style="font-size:12px;margin-top:8px;color:var(--muted)">${eventsData.error || "Check that your PHP session is active and APIs are reachable."}</div>
            </div>`;
            return;
        }

        dashboardEvents = eventsData.events || [];
        renderDashboard(dashboardEvents, kpiData);

        // Update sidebar badge with live role-relevant info
        updateSidebarBadge(kpiData);

        // Load role-specific panels after dashboard renders
        if (kpiData.role === "organiser") {
            loadPendingRequestsPanel();
            loadGuestListPanel();
        } else if (kpiData.role === "admin") {
            loadEventHealth();
        }

    } catch (err) {
        const mc2 = getMain();
        if (mc2) mc2.innerHTML = `<div style="text-align:center;padding:60px;color:#fb7185;">
            <div style="font-size:2rem;margin-bottom:12px;">⚠️</div>
            <div>Dashboard error: ${err.message}</div>
        </div>`;
    }
}

function renderDashboard(events, kpi = {}) {
    const today          = new Date().toISOString().split("T")[0];
    const conflictIDs    = getConflictingEventIDs(events);
    const nearCapacity   = events.filter(ev => ev.Capacity > 0 && (ev.Registered / ev.Capacity) >= 0.85);

    // Deduplicate by EventID before filtering (safety net for any leftover DB duplicates)
    const seenIDs     = new Set();
    const uniqueEvents = events.filter(ev => {
        if (seenIDs.has(ev.EventID)) return false;
        seenIDs.add(ev.EventID);
        return true;
    });

    // Today: only CONFIRMED events, deduplicated
    const todayEvents    = uniqueEvents.filter(ev => ev.EventDate === today && ev.Status === "CONFIRMED");
    const upcomingEvents = uniqueEvents.filter(ev => ev.EventDate >= today).slice(0, 7);

    // KPI values — use API numbers if available, fallback to client-side counts
    const kpiActive   = kpi.success ? kpi.activeEvents    : events.length;
    const kpiOpenDays = kpi.success ? kpi.openDays        : "—";
    const kpiAlerts   = kpi.success ? kpi.capacityAlerts  : nearCapacity.length;
    const kpiPending  = kpi.success ? kpi.pendingRequests : "—";

    // Dynamic KPI labels based on role (returned by kpi-stats.php)
    const label1 = kpi.label1 || "Active Events";
    const label2 = kpi.label2 || "Open Days";
    const label3 = kpi.label3 || "Capacity Alerts";
    const label4 = kpi.label4 || "Resource Requests";

    const sub1 = kpi.role === "organiser" ? "You are organising"
               : kpi.role === "requester" ? "Confirmed & available"
               : "Confirmed across all venues";
    const sub2 = kpi.role === "organiser" ? "Upcoming this week"
               : kpi.role === "requester" ? "You are registered for"
               : "Next 7 days with no events";
    const sub3 = kpi.role === "organiser" ? "Your events above 85%"
               : kpi.role === "requester" ? "Your total bookings"
               : "Events above 85% capacity";
    const sub4 = kpi.role === "organiser" ? "Across all your events"
               : kpi.role === "requester" ? "Days free this week"
               : "Pending approvals";

    getMain().innerHTML = `
        <div class="kpi-grid" id="kpiRow">
            <div class="kpi-card">
                <h3>${label1}</h3>
                <div class="num">${kpiActive}</div>
                <div class="sub">${sub1}</div>
            </div>
            <div class="kpi-card">
                <h3>${label2}</h3>
                <div class="num">${kpiOpenDays}</div>
                <div class="sub">${sub2}</div>
            </div>
            <div class="kpi-card">
                <h3>${label3}</h3>
                <div class="num" style="color:${kpiAlerts > 0 ? '#fbbf24' : 'inherit'}">${kpiAlerts}</div>
                <div class="sub">${sub3}</div>
            </div>
            <div class="kpi-card">
                <h3>${label4}</h3>
                <div class="num" style="color:${kpiPending > 0 ? '#38bdf8' : 'inherit'}">${kpiPending}</div>
                <div class="sub">${sub4}</div>
            </div>
        </div>

        ${kpi.role === "organiser" ? `
        <div class="panel-card" style="margin-bottom:24px;">
            <h3>📋 Pending Approval Requests</h3>
            <p>Attendees waiting for your approval to join your events.</p>
            <div class="divider"></div>
            <div id="pendingRequestsPanel">
                <div style="color:var(--muted);font-size:13px;padding:12px 0;text-align:center;">Loading requests...</div>
            </div>
        </div>
        ` : ""}

        <div class="two-col">
            <div class="panel-card">
                <h3>${kpi.role === "requester" ? "Your Events Today" : kpi.role === "organiser" ? "📝 To-Do List" : "Today's Confirmed Events"}</h3>
                <p>${today}</p>
                <div class="divider"></div>
                <table class="table">
                    <thead><tr><th>Event</th><th>Venue</th><th>Time</th><th>Capacity</th></tr></thead>
                    <tbody>
                        ${todayEvents.length === 0
                            ? `<tr><td colspan="4" style="text-align:center;color:var(--muted);padding:16px;">
                                ${kpi.role === "requester"
                                    ? "You have no events today."
                                    : kpi.role === "organiser"
                                    ? "You have no events scheduled for today."
                                    : "No confirmed events today."}
                               </td></tr>`
                            : todayEvents.map(ev => {
                                const pct  = ev.Capacity > 0 ? Math.round((ev.Registered / ev.Capacity) * 100) : 0;
                                const cls  = pct >= 95 ? "danger" : pct >= 85 ? "warn" : "ok";
                                const lbl  = pct >= 95 ? "Full" : pct >= 85 ? "Near capacity" : "Confirmed";
                                return `<tr>
                                    <td><b>${ev.EventName || "Untitled"}</b></td>
                                    <td>${ev.VenueName || "—"}</td>
                                    <td>${ev.TimeSlot  || "—"}</td>
                                    <td><span class="badge ${cls}">${lbl} (${ev.Registered}/${ev.Capacity})</span></td>
                                </tr>`;
                              }).join("")
                        }
                    </tbody>
                </table>
            </div>

            <div class="panel-card">
                ${kpi.role === "requester" ? `
                    <h3>Your Upcoming Events</h3>
                    <p>Events you are registered for.</p>
                    <div class="divider"></div>
                    <div style="display:grid;gap:10px;margin-top:8px;">
                        ${upcomingEvents.length === 0
                            ? `<div style="color:var(--muted);font-size:14px;padding:8px 0;">You have no upcoming registrations. Browse the Events tab to find events.</div>`
                            : upcomingEvents.slice(0, 4).map(ev => `
                                <div style="background:var(--panel2);padding:12px;border-radius:10px;">
                                    <b>${ev.EventName}</b><br>
                                    <small style="color:var(--muted)">${ev.VenueName || "—"} • ${ev.EventDate || ""}</small>
                                </div>`).join("")
                        }
                        <button class="btn" style="width:100%;margin-top:4px;" onclick="setActiveTab('events')">
                            Browse All Events
                        </button>
                    </div>
                ` : kpi.role === "organiser" ? `
                    <h3>👥 Guest List</h3>
                    <p>Attendees registered across your events.</p>
                    <div class="divider"></div>
                    <div id="guestListPanel" style="max-height:320px;overflow-y:auto;margin-top:4px;">
                        <div style="color:var(--muted);font-size:13px;padding:12px 0;text-align:center;">Loading guests...</div>
                    </div>
                    <div class="divider"></div>
                    <button class="btn" style="width:100%;margin-top:4px;" onclick="setActiveTab('attendees')">
                        View Full Attendees Tab
                    </button>
                ` : kpi.role === "admin" ? `
                    <h3>📊 Event Health Overview</h3>
                    <p>A snapshot of how all confirmed events are performing.</p>
                    <div class="divider"></div>
                    <div id="eventHealthPanel" style="max-height:320px;overflow-y:auto;">
                        <div style="color:var(--muted);font-size:13px;padding:12px 0;text-align:center;">Loading...</div>
                    </div>
                    <div class="divider"></div>
                    <button class="btn" style="width:100%;" onclick="setActiveTab('events')">
                        View All Events
                    </button>
                ` : ``}
            </div>
        </div>

        <div class="week-card" style="margin-top:28px;">
            <h3>${kpi.role === "organiser" ? "My Upcoming Events" : kpi.role === "requester" ? "All Upcoming Events" : "Upcoming Events"}</h3>
            <p>${kpi.role === "organiser" ? "Events you are organising — sorted by date." : kpi.role === "requester" ? "Browse confirmed events coming up." : "Next events across all venues — sorted by date."}</p>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px;margin-top:20px;">
                ${upcomingEvents.length === 0
                    ? `<div style="color:var(--muted);padding:16px;">No upcoming events.</div>`
                    : upcomingEvents.map(ev => {
                        const pct    = ev.Capacity > 0 ? (ev.Registered / ev.Capacity) * 100 : 0;
                        const isNear = pct >= 85;
                        const bg     = isNear ? "#fbbf2433" : "#4ade8033";
                        const dateObj  = new Date(ev.EventDate + "T00:00:00");
                        const dayName  = dateObj.toLocaleDateString("en-US", { weekday: "short" });
                        const dateDisp = dateObj.toLocaleDateString("en-US", { month: "short", day: "numeric" });
                        return `<div style="background:var(--panel2);padding:16px;border-radius:14px;text-align:center;">
                            <h4 style="margin:0 0 4px;">${dayName}</h4>
                            <small style="color:var(--muted)">${dateDisp}</small>
                            <div style="background:${bg};padding:10px;border-radius:10px;margin:8px 0;font-size:12px;">
                                <b>${ev.EventName || "Untitled"}</b><br>
                                <small>${ev.VenueName || "—"}</small><br>
                                <small>${ev.TimeSlot || ""}</small>
                            </div>
                            ${isNear ? `<span class="badge warn" style="font-size:10px;">${Math.round(pct)}% full</span>`
                                     : `<span class="badge ok" style="font-size:10px;">OK</span>`}
                        </div>`;
                    }).join("")
                }
            </div>
        </div>`;
}

async function loadEventHealth() {
    const panel = document.getElementById("eventHealthPanel");
    if (!panel) return;

    const data = await fetchJSON("api/event-health.php");
    if (!data.success) {
        panel.innerHTML = `<div style="color:#fb7185;font-size:13px;padding:12px 0;text-align:center;">Failed to load event health data.</div>`;
        return;
    }

    // ── Summary stat tiles ───────────────────────────────────────
    const avgColor = data.avgFill >= 85 ? "#fb7185"
                   : data.avgFill >= 50 ? "#fbbf24"
                   : "#4ade80";

    const tiles = `
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:14px;">
            <div style="background:var(--glass);border-radius:10px;padding:10px;text-align:center;">
                <div style="font-size:22px;font-weight:700;color:#4ade80;">${data.totalConfirmed}</div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px;">Confirmed Events</div>
            </div>
            <div style="background:var(--glass);border-radius:10px;padding:10px;text-align:center;">
                <div style="font-size:22px;font-weight:700;color:#38bdf8;">${data.thisMonth}</div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px;">This Month</div>
            </div>
            <div style="background:var(--glass);border-radius:10px;padding:10px;text-align:center;">
                <div style="font-size:22px;font-weight:700;color:#fbbf24;">${data.nearCapacityCount}</div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px;">Near Capacity</div>
            </div>
            <div style="background:var(--glass);border-radius:10px;padding:10px;text-align:center;">
                <div style="font-size:22px;font-weight:700;color:${avgColor};">${data.avgFill}%</div>
                <div style="font-size:11px;color:var(--muted);margin-top:2px;">Avg Fill Rate</div>
            </div>
        </div>`;

    // ── Most popular event ───────────────────────────────────────
    const popularSection = data.mostPopular ? `
        <div style="border-top:1px solid var(--glass-border);padding-top:10px;margin-bottom:10px;">
            <div style="font-size:11px;color:var(--muted);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px;">🏆 Most Popular</div>
            <div style="display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:13px;font-weight:500;flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                    ${data.mostPopular.EventName}
                </span>
                <span class="badge ${data.mostPopular.FillPct >= 95 ? "danger" : "warn"}" style="font-size:11px;flex-shrink:0;margin-left:8px;">
                    ${data.mostPopular.FillPct}% full
                </span>
            </div>
            <div style="background:var(--glass);border-radius:4px;height:5px;margin-top:6px;overflow:hidden;">
                <div style="width:${Math.min(data.mostPopular.FillPct, 100)}%;height:100%;background:#fbbf24;border-radius:4px;"></div>
            </div>
            <div style="font-size:11px;color:var(--muted);margin-top:3px;">${data.mostPopular.Registered} / ${data.mostPopular.Capacity} registered</div>
        </div>` : "";

    // ── Near capacity list ───────────────────────────────────────
    const nearSection = data.nearCapacity.length > 0 ? `
        <div style="border-top:1px solid var(--glass-border);padding-top:10px;margin-bottom:10px;">
            <div style="font-size:11px;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px;">⚠️ Near Capacity (${data.nearCapacityCount})</div>
            ${data.nearCapacity.slice(0, 3).map(ev => `
                <div style="display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--glass-border);">
                    <span style="font-size:12.5px;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${ev.EventName}</span>
                    <span class="badge danger" style="font-size:10.5px;flex-shrink:0;margin-left:8px;">${ev.FillPct}%</span>
                </div>`).join("")}
        </div>` : "";

    // ── Empty events list ────────────────────────────────────────
    const emptySection = data.emptyCount > 0 ? `
        <div style="border-top:1px solid var(--glass-border);padding-top:10px;">
            <div style="font-size:11px;color:var(--muted);margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px;">📭 No Registrations Yet (${data.emptyCount})</div>
            ${data.emptyEvents.slice(0, 3).map(ev => `
                <div style="display:flex;align-items:center;justify-content:space-between;padding:5px 0;border-bottom:1px solid var(--glass-border);">
                    <span style="font-size:12.5px;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${ev.EventName}</span>
                    <span style="font-size:11px;color:var(--muted);flex-shrink:0;margin-left:8px;">${ev.EventDate}</span>
                </div>`).join("")}
        </div>` : `
        <div style="border-top:1px solid var(--glass-border);padding-top:10px;">
            <div style="font-size:13px;color:#4ade80;text-align:center;padding:6px 0;">✅ All events have registrations</div>
        </div>`;

    panel.innerHTML = tiles + popularSection + nearSection + emptySection;
}

// ==================== TIPS MODAL ====================
function showTipsModal() {
    const tips = {
        admin: ["Use the Conflict chip in Quick Filters to instantly see overlapping bookings.",
            "Run Scheduling Check on the dashboard to get a live summary of all issues.",
            "Near Capacity filter shows events above 85% — act early to avoid overbooking.",
            "Use the Reports tab to generate and export filtered CSV reports by date and venue.",
            "Validate venue + time before saving to catch conflicts before they happen."],
        organiser: ["Always click Validate before saving to check for venue conflicts.",
            "You can book any date and time — the system prevents double-bookings automatically.",
            "Attendee count is your event's expected guests, max 300 total venue capacity.",
            "Use the Schedule tab to see what's already booked on a specific date.",
            "Near Capacity filter helps you find events that may need attention."],
        requester: ["Browse the Events tab to see all upcoming events.",
            "Use the date filter to find events on a specific day.",
            "Check the Attendees tab to see registration details.",
            "Contact an organiser if you need to register for a near-capacity event."]
    };
    const roleTips = tips[state.role] || tips["requester"];
    const existing = document.getElementById("tipsModal");
    if (existing) existing.remove();
    const modal = document.createElement("div");
    modal.id = "tipsModal";
    modal.style.cssText = "position:fixed;inset:0;background:#0008;z-index:1000;display:flex;align-items:center;justify-content:center;";
    modal.innerHTML = `
        <div style="background:var(--panel);border-radius:18px;padding:32px;min-width:340px;max-width:460px;box-shadow:0 8px 40px #0006;">
            <h3 style="margin:0 0 6px;">Tips for ${roleLabel(state.role)}s</h3>
            <p style="color:var(--muted);margin:0 0 20px;font-size:13px;">Quick guide to using this dashboard.</p>
            <ul style="padding-left:18px;margin:0 0 24px;display:flex;flex-direction:column;gap:10px;">
                ${roleTips.map(tip => `<li style="font-size:14px;line-height:1.5;">${tip}</li>`).join("")}
            </ul>
            <button class="btn primary" style="width:100%;" id="closeTipsBtn">Got it</button>
        </div>`;
    document.body.appendChild(modal);
    document.getElementById("closeTipsBtn").onclick = () => modal.remove();
    modal.onclick = (e) => { if (e.target === modal) modal.remove(); };
}

// ==================== QUICK FILTERS ====================
function applyQuickFilters() {
    const venueFilter  = el("venueFilter")?.value || "all";
    const dateFilter   = el("dateFilter")?.value  || "";
    const activeChip   = document.querySelector(".chip.active");
    const statusFilter = activeChip ? activeChip.dataset.status : "all";
    let filtered = [...allEventsCache];
    if (venueFilter !== "all") filtered = filtered.filter(ev => (ev.VenueName || "").toLowerCase().includes(venueFilter.toLowerCase()));
    if (dateFilter) filtered = filtered.filter(ev => ev.EventDate === dateFilter);
    if (statusFilter === "warn") {
        filtered = filtered.map(ev => ({ ...ev, pct: ev.Capacity > 0 ? (ev.Registered / ev.Capacity) * 100 : 0 }))
            .filter(ev => ev.pct >= 85).sort((a, b) => b.pct - a.pct);
    } else if (statusFilter === "danger") {
        const conflictIDs = getConflictingEventIDs(allEventsCache);
        filtered = filtered.filter(ev => conflictIDs.has(String(ev.EventID)));
    }
    const tbody = el("eventsTableBody");
    if (!tbody) return;
    const emptyMsg = { "warn": "No events above 85% capacity.", "danger": "No conflicts detected.", "all": "No events match filters." };
    tbody.innerHTML = filtered.length === 0
        ? `<tr><td colspan="6" style="text-align:center;color:var(--muted);padding:24px;">${emptyMsg[statusFilter] || emptyMsg["all"]}</td></tr>`
        : filtered.map(ev => buildEventRow(ev)).join("");
}

// ==================== EVENT ROW BUILDER ====================
// buildEventRow for admin/organiser only — requester uses dedicated functions below
function buildEventRow(ev, canCreate = window.canCreateEvent) {
    const statusMap      = { "CONFIRMED": { cls: "ok", label: "Confirmed" }, "DRAFT": { cls: "warn", label: "Draft" }, "CANCELLED": { cls: "danger", label: "Cancelled" } };
    const badge          = statusMap[ev.Status] || { cls: "ok", label: ev.Status || "OK" };
    const registered     = ev.Registered     || 0;
    const capacity       = ev.Capacity       || "—";
    const capacityStatus = ev.CapacityStatus || "ok";
    const dateTime       = [ev.EventDate, ev.TimeSlot].filter(Boolean).join(" ");
    const evJson         = JSON.stringify(ev).replace(/'/g, "&#39;");
    const pct            = (ev.Capacity > 0 && ev.Registered > 0)
        ? ` (${Math.round((ev.Registered / ev.Capacity) * 100)}%)` : "";
    const actionCol      = canCreate
        ? `<td><button class="btn small" onclick='editEvent(${ev.EventID}, ${evJson})'>Edit</button></td>`
        : "";
    const privacyTag = ev.IsPrivate == 1
        ? ` <span title="Private — invite only" style="font-size:11px;vertical-align:middle;">🔒</span>`
        : "";
    return `<tr>
        <td><b>${ev.EventName || "Untitled"}</b>${privacyTag}${ev.Organizer ? `<br><small style="color:var(--muted)">${ev.Organizer}</small>` : ""}</td>
        <td>${ev.VenueName || "—"}</td>
        <td>${dateTime    || "—"}</td>
        <td><span class="badge ${capacityStatus}">${registered} / ${capacity}${pct}</span></td>
        <td><span class="badge ${badge.cls}">${badge.label}</span></td>
        ${actionCol}
    </tr>`;
}

// Build a row for the requester Available Events table (Register button)
function buildAvailableRow(ev) {
    const registered     = ev.Registered     || 0;
    const capacity       = ev.Capacity       || "—";
    const capacityStatus = ev.CapacityStatus || "ok";
    const isFull         = ev.Capacity > 0 && ev.Registered >= ev.Capacity;
    const dateTime       = [ev.EventDate, ev.TimeSlot].filter(Boolean).join(" ");
    const pct            = (ev.Capacity > 0 && ev.Registered > 0)
        ? ` (${Math.round((ev.Registered / ev.Capacity) * 100)}%)` : "";
    const regStatus      = (ev.RegistrationStatus || "").toUpperCase();
    const isPending      = regStatus === "PENDING";

    let actionCol;
    if (isPending) {
        // Already submitted — waiting for approval, show pending label only
        actionCol = `<td><span class="badge warn">⏳ Pending approval</span></td>`;
    } else if (isFull) {
        actionCol = `<td><span class="badge danger">Full</span></td>`;
    } else {
        const safeName = (ev.EventName || "").replace(/'/g, "").replace(/"/g, "");
        actionCol = `<td>
            <button class="btn small primary" onclick="registerForEvent(${ev.EventID}, '${safeName}')">
                Register
            </button>
        </td>`;
    }

    return `<tr>
        <td>
            <b>${ev.EventName || "Untitled"}</b>
            ${ev.Organizer ? `<br><small style="color:var(--muted)">${ev.Organizer}</small>` : ""}
        </td>
        <td>${ev.VenueName || "—"}</td>
        <td>${dateTime    || "—"}</td>
        <td><span class="badge ${capacityStatus}">${registered} / ${capacity}${pct}</span></td>
        ${actionCol}
    </tr>`;
}

// Build a row for the requester Upcoming Events table (Cancel button only)
function buildUpcomingRow(ev) {
    const registered     = ev.Registered     || 0;
    const capacity       = ev.Capacity       || "—";
    const capacityStatus = ev.CapacityStatus || "ok";
    const dateTime       = [ev.EventDate, ev.TimeSlot].filter(Boolean).join(" ");
    const pct            = (ev.Capacity > 0 && ev.Registered > 0)
        ? ` (${Math.round((ev.Registered / ev.Capacity) * 100)}%)` : "";
    return `<tr>
        <td>
            <b>${ev.EventName || "Untitled"}</b>
            ${ev.Organizer ? `<br><small style="color:var(--muted)">${ev.Organizer}</small>` : ""}
        </td>
        <td>${ev.VenueName || "—"}</td>
        <td>${dateTime    || "—"}</td>
        <td><span class="badge ${capacityStatus}">${registered} / ${capacity}${pct}</span></td>
        <td>
            <span class="badge ok" style="margin-right:6px;">✅ Approved</span>
            <button class="btn small danger" onclick="cancelRegistration(${ev.EventID})">Cancel</button>
        </td>
    </tr>`;
}

// ==================== CAPACITY VALIDATION ====================
function setCapacityError(show) {
    const cap = el("evCapacity"), errorMsg = el("evCapacityError");
    if (!cap || !errorMsg) return;
    if (show) { cap.style.border = "2px solid #fb7185"; cap.style.boxShadow = "0 0 0 3px #fb718540"; cap.style.color = "#fb7185"; errorMsg.style.display = "block"; }
    else { cap.style.border = ""; cap.style.boxShadow = ""; cap.style.color = ""; errorMsg.style.display = "none"; }
}
function isCapacityInvalid() { return parseInt(el("evCapacity")?.value || "0") > 300; }

// ==================== EVENT ACTION MODAL ====================
let modalTargetEvent = null;

function openEventActionModal(ev) {
    modalTargetEvent = ev;
    const modal = el("eventActionModal");
    if (!modal) return;

    el("modalEventTitle").textContent = ev.EventName || ev.Title || "Untitled";
    el("modalEventMeta").textContent  = `${ev.VenueName || "Unknown Venue"} • ${ev.EventDate || ""} ${ev.TimeSlot || ""}`.trim();

    // Ownership: organiser can only edit their own events; only admin can delete
    const isAdmin   = window.currentUserRole === "admin";
    const isOwner   = String(ev.OrganizerID) === String(window.currentUserID);
    const canEdit   = isAdmin || (window.currentUserRole === "organiser" && isOwner);
    const canDelete = isAdmin;

    const editBtn   = el("modalEditBtn");
    const deleteBtn = el("modalDeleteBtn");

    if (editBtn) {
        editBtn.style.display = canEdit ? "" : "none";
        editBtn.onclick = () => {
            modal.style.display = "none";
            setActiveTab("events");
            setTimeout(() => loadEventIntoForm(modalTargetEvent.EventID), 350);
        };
    }

    if (deleteBtn) {
        deleteBtn.style.display = canDelete ? "" : "none";
        deleteBtn.onclick = () => { modal.style.display = "none"; deleteEvent(modalTargetEvent.EventID); };
    }

    el("modalCancelBtn").onclick = () => { modal.style.display = "none"; modalTargetEvent = null; };
    modal.onclick = (e) => { if (e.target === modal) { modal.style.display = "none"; modalTargetEvent = null; } };
    modal.style.display = "flex";
}

async function loadEventIntoForm(eventID) {
    state.editingEventId = eventID;
    showToast("Loading", `Loading Event #${eventID}...`);
    const result = await fetchJSON(`api/get-event.php?eventID=${eventID}`);
    if (result.success && result.event) {
        const ev = result.event;
        if (el("evName"))      el("evName").value      = ev.Title         || "";
        if (el("evCapacity"))  el("evCapacity").value  = ev.CapacityLimit || "";
        if (el("evDesc"))      el("evDesc").value       = ev.Description   || "";
        if (el("evDate"))      el("evDate").value       = ev.EventDate     || "";
        if (el("evStartTime")) el("evStartTime").value  = ev.StartTime     || "";
        if (el("evEndTime"))   el("evEndTime").value    = ev.EndTime       || "";
        await loadVenueOptions();
        if (el("evVenue")) el("evVenue").value = ev.VenueID || "";
        showToast("Ready", "Event loaded — make your changes and hit Save.");
    } else showToast("Error", "Failed to load event data.");
}

async function deleteEvent(eventID) {
    if (!confirm(`Permanently delete Event #${eventID}? This cannot be undone.`)) return;
    const result = await fetchJSON("api/delete-event.php", { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ eventID }) });
    if (result.success) { showToast("Deleted", "Event removed successfully."); loadEventsTab(); }
    else showToast("Error", result.error || "Failed to delete event.");
}

function editEvent(eventID, ev) { openEventActionModal(ev); }
window.editEvent   = editEvent;
window.deleteEvent = deleteEvent;

// ==================== EVENTS TAB ====================
async function loadEventsTab() {
    const data = await fetchJSON("api/events-list.php");
    if (data.success && data.events) { allEventsCache = data.events; renderEventsTab(data.events); }
    else getMain().innerHTML = `<div class="panel"><h3>Events</h3><p style="color:#fb7185;">Unable to load events. ${data.error || ""}</p></div>`;
}

function renderEventsTab(events) {
    const canCreate   = window.canCreateEvent === true;
    const isRequester = window.currentUserRole === "requester";

    if (isRequester) {
        // ── REQUESTER: split into Upcoming (APPROVED) and Available (rest) ──
        const upcoming  = events.filter(ev => (ev.RegistrationStatus || "").toUpperCase() === "APPROVED");
        const available = events.filter(ev => (ev.RegistrationStatus || "").toUpperCase() !== "APPROVED");

        getMain().innerHTML = `
            <!-- Upcoming Events: approved registrations with Cancel button -->
            <div class="panel" style="margin-bottom:24px;">
                <h3>📅 My Upcoming Events</h3>
                <p>Events you are registered and approved for. Click <b>Cancel</b> to withdraw.</p>
                <div class="divider"></div>
                <table class="table">
                    <thead>
                        <tr><th>Event</th><th>Venue</th><th>Date & Time</th><th>Spots</th><th>Action</th></tr>
                    </thead>
                    <tbody id="upcomingTableBody"></tbody>
                </table>
            </div>

            <!-- Available Events: not yet approved, can register -->
            <div class="panel">
                <h3>🔍 Available Events</h3>
                <p>Click <b>Register</b> to request a spot. Events move to Upcoming once approved.</p>
                <div class="divider"></div>
                <table class="table">
                    <thead>
                        <tr><th>Event</th><th>Venue</th><th>Date & Time</th><th>Spots</th><th>Action</th></tr>
                    </thead>
                    <tbody id="availableTableBody"></tbody>
                </table>
            </div>`;

        // Populate Upcoming table
        const upcomingBody = el("upcomingTableBody");
        if (upcomingBody) {
            upcomingBody.innerHTML = upcoming.length === 0
                ? `<tr><td colspan="5" style="text-align:center;color:var(--muted);padding:20px;">
                    No approved registrations yet. Register for an event below.
                   </td></tr>`
                : upcoming.map(ev => buildUpcomingRow(ev)).join("");
        }

        // Populate Available table
        const availableBody = el("availableTableBody");
        if (availableBody) {
            availableBody.innerHTML = available.length === 0
                ? `<tr><td colspan="5" style="text-align:center;color:var(--muted);padding:20px;">
                    No available events at this time.
                   </td></tr>`
                : available.map(ev => buildAvailableRow(ev)).join("");
        }
        return;
    }

    // ── ADMIN / ORGANISER: single list with Edit button ────────────────────
    getMain().innerHTML = `
        <div class="two-col">
            <div class="panel">
                <h3>Create / Edit Event</h3>
                <p>Pick any date and time — conflicts are checked automatically.</p>
                <div class="divider"></div>
                <div class="field"><label>Event Name</label><input id="evName" placeholder="e.g., Wedding Reception" /></div>
                <div class="field"><label>Venue</label><select id="evVenue"><option value="">Select Venue</option></select></div>
                <div class="field"><label>Event Date</label><input type="date" id="evDate" /></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                    <div class="field"><label>Start Time</label><input type="time" id="evStartTime" /></div>
                    <div class="field"><label>End Time</label><input type="time" id="evEndTime" /></div>
                </div>
                <div class="field">
                    <label>Number of Attendees <small style="color:var(--muted);margin-left:6px;">(out of 300 total)</small></label>
                    <input id="evCapacity" type="number" min="1" max="300" placeholder="e.g., 80 out of 300"
                        style="transition:border 0.2s,box-shadow 0.2s,color 0.2s;" />
                    <small id="evCapacityError" style="color:#fb7185;display:none;margin-top:5px;font-size:12px;">
                        ⚠️ Cannot exceed 300 (total venue capacity).
                    </small>
                </div>
                <div class="field"><label>Description</label><textarea id="evDesc" placeholder="Describe the event..."></textarea></div>

                <!-- Privacy toggle -->
                <div class="field" style="margin-top:4px;">
                    <label>Event Visibility</label>
                    <div style="display:flex;gap:12px;margin-top:6px;">
                        <label style="display:flex;align-items:center;gap:7px;cursor:pointer;font-size:13.5px;">
                            <input type="radio" name="evPrivacy" id="evPublic" value="public" checked
                                style="accent-color:var(--primary);width:15px;height:15px;" />
                            🌐 Public <small style="color:var(--muted);margin-left:2px;">— anyone can register</small>
                        </label>
                        <label style="display:flex;align-items:center;gap:7px;cursor:pointer;font-size:13.5px;">
                            <input type="radio" name="evPrivacy" id="evPrivate" value="private"
                                style="accent-color:var(--primary);width:15px;height:15px;" />
                            🔒 Private <small style="color:var(--muted);margin-left:2px;">— invite only</small>
                        </label>
                    </div>
                </div>

                <!-- Invite emails — shown only when Private is selected -->
                <div class="field" id="evInviteField" style="display:none;margin-top:2px;">
                    <label>Invite Attendees
                        <small style="color:var(--muted);font-weight:normal;margin-left:6px;">
                            — enter email addresses, one per line
                        </small>
                    </label>
                    <textarea id="evInviteEmails" rows="4"
                        placeholder="alice@festival.edu&#10;bob@festival.edu&#10;carol@festival.edu"
                        style="font-size:12.5px;resize:vertical;"></textarea>
                    <small style="color:var(--muted);font-size:11.5px;margin-top:4px;display:block;">
                        ✅ Invited attendees are automatically approved. Emails not found in the system will be listed after saving.
                    </small>
                </div>

                <div style="display:flex;gap:10px;margin-top:12px;">
                    <button class="btn primary" id="evSaveBtn" type="button">Create Event</button>
                    <button class="btn" id="evValidateBtn" type="button">Validate</button>
                </div>
            </div>
            <div class="panel">
                <h3>Event List</h3>
                <p>Live data from the database. Click Edit to manage an event.</p>
                <div class="divider"></div>
                <table class="table">
                    <thead><tr><th>Event</th><th>Venue</th><th>Date & Time</th><th>Spots</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody id="eventsTableBody"></tbody>
                </table>
            </div>
        </div>`;

    const tbody = el("eventsTableBody");
    if (tbody) {
        tbody.innerHTML = events.length === 0
            ? `<tr><td colspan="6" style="text-align:center;color:var(--muted);padding:24px;">No events found.</td></tr>`
            : events.map(ev => buildEventRow(ev, canCreate)).join("");
    }

    loadVenueOptions();
    setTimeout(() => {
        el("evSaveBtn")    ?.addEventListener("click", saveEvent);
        el("evValidateBtn")?.addEventListener("click", validateEvent);
        el("evCapacity")   ?.addEventListener("input", () => setCapacityError(isCapacityInvalid()));

        // Show/hide invite field based on privacy selection
        document.querySelectorAll('input[name="evPrivacy"]').forEach(radio => {
            radio.addEventListener("change", () => {
                const inviteField = el("evInviteField");
                if (inviteField) {
                    inviteField.style.display = el("evPrivate")?.checked ? "block" : "none";
                }
            });
        });
    }, 100);
}

// ==================== REGISTER / CANCEL ====================
async function registerForEvent(eventID, eventName) {
    if (!confirm(`Register for "${eventName}"?`)) return;

    const result = await fetchJSON("api/register-event.php", {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify({ eventID, action: "register" })
    });

    if (result.success) {
        showToast("Registered! 🎉", result.message);
        loadEventsTab();   // refresh to update button states
    } else {
        showToast("Error", result.error || "Could not register.");
    }
}

async function cancelRegistration(eventID) {
    if (!confirm("Cancel your registration for this event?")) return;

    const result = await fetchJSON("api/register-event.php", {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify({ eventID, action: "cancel" })
    });

    if (result.success) {
        showToast("Cancelled", result.message);
        loadEventsTab();
    } else {
        showToast("Error", result.error || "Could not cancel registration.");
    }
}

window.registerForEvent  = registerForEvent;
window.cancelRegistration = cancelRegistration;

async function loadVenueOptions() {
    const venuesData  = await fetchJSON("api/venues.php");
    const venueSelect = el("evVenue");
    if (venueSelect && venuesData.success && venuesData.venues) {
        venueSelect.innerHTML = '<option value="">Select Venue</option>' +
            venuesData.venues.map(v => `<option value="${v.VenueID}">${v.VenueName}</option>`).join("");
    }
}

async function saveEvent() {
    const name = el("evName")?.value.trim(), venueId = el("evVenue")?.value;
    const eventDate = el("evDate")?.value, startTime = el("evStartTime")?.value;
    const endTime = el("evEndTime")?.value, capacity = parseInt(el("evCapacity")?.value || "0");
    const desc = el("evDesc")?.value.trim();
    if (!name || !venueId || !eventDate || !startTime || !endTime || !capacity) { showToast("Error", "Please fill all required fields."); return; }
    if (isCapacityInvalid()) { setCapacityError(true); el("evCapacity")?.focus(); showToast("Error", "Number of attendees cannot exceed 300."); return; }
    if (startTime >= endTime) { showToast("Error", "End time must be after start time."); return; }

    // Read privacy setting and invite emails
    const isPrivate    = el("evPrivate")?.checked ? true : false;
    const inviteRaw    = el("evInviteEmails")?.value || "";
    const inviteEmails = isPrivate
        ? inviteRaw.split("\n").map(e => e.trim()).filter(e => e.length > 0)
        : [];

    // Validate: private event must have at least one invite
    if (isPrivate && inviteEmails.length === 0) {
        showToast("Error", "Please add at least one email address for a private event.");
        el("evInviteEmails")?.focus();
        return;
    }

    const payload = {
        title: name, description: desc, capacity,
        venueId: parseInt(venueId), eventDate, startTime, endTime,
        isPrivate, inviteEmails
    };
    if (state.editingEventId) payload.eventID = state.editingEventId;

    const result = await fetchJSON("api/save-event.php", {
        method: "POST", headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
    });

    if (result.success) {
        // Show warning if some invited emails weren't found in the system
        if (result.warning) {
            showToast("Event Created ⚠️", result.warning);
        } else {
            const msg = isPrivate ? "Private event created & invites sent! ✉️" : "Event saved successfully!";
            showToast("Success 🎉", msg);
        }
        state.editingEventId = null;
        // Reset privacy toggle back to Public after save
        if (el("evPublic")) el("evPublic").checked = true;
        if (el("evInviteField")) el("evInviteField").style.display = "none";
        if (el("evInviteEmails")) el("evInviteEmails").value = "";
        loadEventsTab();
    } else {
        showToast("Error", result.error || "Failed to save event.");
    }
}

function validateEvent() {
    const venueId = el("evVenue")?.value, eventDate = el("evDate")?.value;
    const startTime = el("evStartTime")?.value, endTime = el("evEndTime")?.value;
    if (!venueId || !eventDate || !startTime || !endTime) { showToast("Validate", "Fill in venue, date, and both times first."); return; }
    if (isCapacityInvalid()) { setCapacityError(true); el("evCapacity")?.focus(); showToast("Error", "Fix attendee count before validating."); return; }
    if (startTime >= endTime) { showToast("Error", "End time must be after start time."); return; }
    const conflict = allEventsCache.find(ev => {
        if (String(ev.VenueID) !== String(venueId) || ev.EventDate !== eventDate || String(ev.EventID) === String(state.editingEventId)) return false;
        const parts = (ev.TimeSlot || "").split("–");
        if (parts.length < 2) return false;
        const evStart = parts[0].trim().substring(0, 5), evEnd = parts[1].trim().substring(0, 5);
        return startTime < evEnd && endTime > evStart;
    });
    if (conflict) showToast("Conflict Detected", `"${conflict.EventName}" is already booked at this venue ${conflict.TimeSlot}.`);
    else showToast("No Conflicts", "This venue, date, and time are available.");
}

// ==================== SCHEDULE TAB ====================
function loadScheduleTab() {
    getMain().innerHTML = `
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
            <div class="panel">
                <h3>Availability Finder</h3>
                <p>See what's booked on a specific date and venue.</p>
                <div class="field"><label>Venue</label><select id="scheduleVenue"><option value="">All Venues</option></select></div>
                <div class="field"><label>Date</label><input type="date" id="scheduleDate" /></div>
                <button class="btn primary" id="scheduleSearchBtn" style="width:100%;margin-top:15px;">Search</button>
            </div>
            <div class="panel">
                <h3>Results</h3>
                <p id="scheduleResultsMsg">Select a date and click Search.</p>
                <table class="table" style="margin-top:15px;">
                    <thead><tr><th>Event</th><th>Venue</th><th>Time</th><th>Attendees</th><th>Status</th></tr></thead>
                    <tbody id="scheduleResults"><tr><td colspan="5" style="text-align:center;color:var(--muted);padding:16px;">No search run yet.</td></tr></tbody>
                </table>
            </div>
        </div>
        <div class="week-card" style="margin-top:32px;background:var(--panel);padding:24px;border-radius:16px;">
            <h3>Upcoming Events</h3>
            <p>All venues — next 7 events by date.</p>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:14px;margin-top:20px;" id="scheduleWeekGrid">
                <div style="color:var(--muted);padding:16px;">Loading...</div>
            </div>
        </div>`;

    fetchJSON("api/venues.php").then(data => {
        const sel = el("scheduleVenue");
        if (sel && data.success && data.venues) {
            sel.innerHTML = '<option value="">All Venues</option>' +
                data.venues.map(v => `<option value="${v.VenueID}">${v.VenueName}</option>`).join("");
        }
    });

    fetchJSON("api/events-list.php").then(data => {
        const grid = el("scheduleWeekGrid");
        if (!grid) return;
        const events = data.success ? data.events : [];
        const today = new Date().toISOString().split("T")[0];
        const upcoming = events.filter(ev => ev.EventDate >= today).slice(0, 7);
        if (upcoming.length === 0) { grid.innerHTML = `<div style="color:var(--muted);padding:16px;">No upcoming events.</div>`; return; }
        grid.innerHTML = upcoming.map(ev => {
            const dateObj = new Date(ev.EventDate + "T00:00:00");
            const dayName = dateObj.toLocaleDateString("en-US", { weekday: "short" });
            const dateDisp = dateObj.toLocaleDateString("en-US", { month: "short", day: "numeric" });
            return `<div style="background:var(--panel2);padding:16px;border-radius:14px;text-align:center;">
                <h4 style="margin:0 0 4px;">${dayName}</h4>
                <small style="color:var(--muted)">${dateDisp}</small>
                <div style="background:#4ade8033;padding:10px;border-radius:10px;margin:8px 0;font-size:12px;">
                    <b>${ev.EventName || "Untitled"}</b><br>
                    <small>${ev.VenueName || "—"}</small><br>
                    <small>${ev.TimeSlot || ""}</small>
                </div>
            </div>`;
        }).join("");
    });

    setTimeout(() => {
        el("scheduleSearchBtn")?.addEventListener("click", searchAvailability);
    }, 100);
}

async function searchAvailability() {
    const venueId   = el("scheduleVenue")?.value || "";
    const eventDate = el("scheduleDate")?.value  || "";
    if (!eventDate) { showToast("Search", "Please select a date first."); return; }
    const params = new URLSearchParams({ eventDate });
    if (venueId) params.append("venueId", venueId);
    const data  = await fetchJSON(`api/schedule.php?${params}`);
    const tbody = el("scheduleResults");
    const msg   = el("scheduleResultsMsg");
    if (!tbody) return;
    if (!data.success) { tbody.innerHTML = `<tr><td colspan="5" style="color:#fb7185;text-align:center;padding:16px;">Error loading schedule.</td></tr>`; return; }
    if (msg) msg.textContent = `Results for ${eventDate}${venueId ? " at selected venue" : " — all venues"}.`;
    if (data.booked.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:#34d399;padding:16px;">✅ No events booked on this date${venueId ? " at this venue" : ""}. Fully available!</td></tr>`;
        return;
    }
    tbody.innerHTML = data.booked.map(slot => {
        const fillPct = slot.CapacityLimit > 0 ? Math.round((slot.Registered / slot.CapacityLimit) * 100) : 0;
        const cls = fillPct >= 95 ? "danger" : fillPct >= 85 ? "warn" : "ok";
        return `<tr>
            <td><b>${slot.EventName || "—"}</b></td>
            <td>${slot.VenueName || "—"}</td>
            <td>${slot.StartTime || ""} – ${slot.EndTime || ""}</td>
            <td><span class="badge ${cls}">${slot.Registered} / ${slot.CapacityLimit}</span></td>
            <td><span class="badge ok">Booked</span></td>
        </tr>`;
    }).join("");
}

// ==================== RESOURCES TAB ====================
// Schema: resource(ResourceID, Name, Type)
//         event_resource(EventID, ResourceID, Quantity)  ← no Status column
function loadResourcesTab() { renderResourcesTab(); }

async function renderResourcesTab() {
    getMain().innerHTML = `
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
            <div class="panel">
                <h3>Assign Resource to Event</h3>
                <p>Link a resource and quantity to an event.</p>
                <div class="field">
                    <label>Event</label>
                    <select id="resEventSel"><option value="">Select Event</option></select>
                </div>
                <div class="field">
                    <label>Resource</label>
                    <select id="resTypeSel"><option value="">Select Resource</option></select>
                </div>
                <div class="field">
                    <label>Quantity</label>
                    <input type="number" id="resQty" value="1" min="1" />
                </div>
                <button class="btn primary" id="resSubmitBtn" style="width:100%;margin-top:10px;">
                    Assign Resource
                </button>
            </div>
            <div class="panel">
                <h3>Resource Inventory</h3>
                <p>Usage across all events.</p>
                <table class="table" style="margin-top:15px;">
                    <thead>
                        <tr>
                            <th>Resource</th>
                            <th>Type</th>
                            <th>Total Qty Assigned</th>
                            <th>Events Using</th>
                            <th>Demand</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryBody">
                        <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:16px;">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="panel" style="margin-top:24px;">
            <h3>Resource Assignments</h3>
            <p>All resources currently linked to events.</p>
            <table class="table">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Venue</th>
                        <th>Date & Time</th>
                        <th>Resource</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th>Event Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="requestsBody">
                    <tr><td colspan="8" style="text-align:center;color:var(--muted);padding:16px;">Loading...</td></tr>
                </tbody>
            </table>
        </div>`;

    const data = await fetchJSON("api/resources-data.php");

    if (!data.success) {
        showToast("Error", data.error || "Failed to load resource data.");
        el("inventoryBody").innerHTML = `<tr><td colspan="5" style="color:#fb7185;text-align:center;padding:16px;">${data.error || "Error loading resources."}</td></tr>`;
        el("requestsBody").innerHTML  = `<tr><td colspan="8" style="color:#fb7185;text-align:center;padding:16px;">Could not load assignments.</td></tr>`;
        return;
    }

    // Populate event dropdown
    const evSel = el("resEventSel");
    if (evSel) {
        evSel.innerHTML = '<option value="">Select Event</option>' +
            (data.events || []).map(e =>
                `<option value="${e.EventID}">${e.Title}</option>`
            ).join("");
    }

    // Populate resource dropdown
    const resSel = el("resTypeSel");
    if (resSel) {
        resSel.innerHTML = '<option value="">Select Resource</option>' +
            (data.resources || []).map(r =>
                `<option value="${r.ResourceID}">${r.Name} (${r.Type})</option>`
            ).join("");
    }

    // Inventory table
    const invBody = el("inventoryBody");
    if (invBody) {
        const inv = data.inventory || [];
        if (inv.length === 0) {
            invBody.innerHTML = `<tr><td colspan="5" style="text-align:center;color:var(--muted);padding:16px;">No resources found.</td></tr>`;
        } else {
            invBody.innerHTML = inv.map(r => {
                const demandLabel = r.AvailStatus === "danger" ? "Very High"
                                  : r.AvailStatus === "warn"   ? "High"
                                  : "Normal";
                return `<tr>
                    <td><b>${r.ResourceName}</b></td>
                    <td>
                        <span style="background:var(--panel2);padding:3px 8px;border-radius:6px;font-size:12px;">
                            ${r.ResourceType || "—"}
                        </span>
                    </td>
                    <td>${r.TotalAllocated || 0}</td>
                    <td>${r.EventsUsing    || 0}</td>
                    <td><span class="badge ${r.AvailStatus || 'ok'}">${demandLabel}</span></td>
                </tr>`;
            }).join("");
        }
    }

    // Assignments table — no Status column, show EventStatus instead
    const reqBody = el("requestsBody");
    if (reqBody) {
        const reqs = data.requests || [];
        if (reqs.length === 0) {
            reqBody.innerHTML = `<tr><td colspan="8" style="text-align:center;color:var(--muted);padding:16px;">No resource assignments found.</td></tr>`;
        } else {
            reqBody.innerHTML = reqs.map(r => {
                const evCls     = r.EventStatus === "CONFIRMED" ? "ok"
                                : r.EventStatus === "DRAFT"     ? "warn"
                                : "danger";
                const safeEvent = (r.EventName    || "").replace(/'/g, "");
                const safeRes   = (r.ResourceName || "").replace(/'/g, "");
                return `<tr id="res-row-${r.EventID}-${r.ResourceID}">
                    <td><b>${r.EventName || "—"}</b></td>
                    <td>${r.VenueName   || "—"}</td>
                    <td>
                        ${r.EventDate
                            ? `${r.EventDate}<br><small style="color:var(--muted)">${r.TimeSlot || ""}</small>`
                            : "—"}
                    </td>
                    <td>${r.ResourceName || "—"}</td>
                    <td>
                        <span style="background:var(--panel2);padding:3px 8px;border-radius:6px;font-size:12px;">
                            ${r.ResourceType || "—"}
                        </span>
                    </td>
                    <td><b>${r.Quantity || 0}</b></td>
                    <td><span class="badge ${evCls}">${r.EventStatus || "—"}</span></td>
                    <td>
                        <button
                            class="btn small danger"
                            onclick="removeResourceAssignment(${r.EventID}, ${r.ResourceID}, '${safeEvent}', '${safeRes}')"
                            style="font-size:11.5px;padding:4px 10px;">
                            ✕ Remove
                        </button>
                    </td>
                </tr>`;
            }).join("");
        }
    }

    setTimeout(() => {
        el("resSubmitBtn")?.addEventListener("click", submitResourceRequest);
    }, 100);
}

async function submitResourceRequest() {
    const eventId    = el("resEventSel")?.value;
    const resourceId = el("resTypeSel")?.value;
    const qty        = parseInt(el("resQty")?.value || "0");

    if (!eventId || !resourceId || qty <= 0) {
        showToast("Error", "Please select an event, resource, and enter a quantity.");
        return;
    }

    const result = await fetchJSON("api/save-resource.php", {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify({ eventId: parseInt(eventId), resourceId: parseInt(resourceId), quantity: qty })
    });

    if (result.success) {
        showToast("Assigned", "Resource assigned to event successfully.");
        renderResourcesTab();
    } else {
        showToast("Error", result.error || "Failed to assign resource.");
    }
}

// Remove a resource assignment from an event
window.removeResourceAssignment = async function(eventID, resourceID, eventName, resourceName) {
    if (!confirm(`Remove "${resourceName}" from "${eventName}"?\nThis cannot be undone.`)) return;

    // Fade the row immediately
    const row = document.getElementById(`res-row-${eventID}-${resourceID}`);
    if (row) {
        row.style.opacity = "0.3";
        row.querySelectorAll("button").forEach(b => b.disabled = true);
    }

    const result = await fetchJSON("api/remove-resource.php", {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify({ eventID, resourceID })
    });

    if (result.success) {
        showToast("Removed", `"${resourceName}" removed from "${eventName}".`);
        // Full refresh of resources tab so inventory counts update too
        renderResourcesTab();
    } else {
        showToast("Error", result.error || "Could not remove resource.");
        if (row) {
            row.style.opacity = "1";
            row.querySelectorAll("button").forEach(b => b.disabled = false);
        }
    }
};

// ==================== ATTENDEES TAB ====================
function loadAttendeesTab() {
    renderAttendeesTab();
    // Always fetch fresh data when switching to Attendees tab
    // so any booking changes made elsewhere are immediately visible
}

async function renderAttendeesTab(search = "", eventId = "", status = "") {
    getMain().innerHTML = `
        <div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;">
            <div class="panel">
                <h3>Controls</h3>
                <p>Filter registrations from the database.</p>
                <div class="field"><label>Search attendee</label><input type="text" id="attSearch" placeholder="Name or email" value="${search}" /></div>
                <div class="field"><label>Event</label><select id="attEventSel"><option value="">All events</option></select></div>
                <div class="field"><label>Status</label>
                    <select id="attStatus">
                        <option value="">All</option>
                        <option value="APPROVED"  \${status === "APPROVED"  ? "selected" : ""}>Approved</option>
                        <option value="PENDING"   \${status === "PENDING"   ? "selected" : ""}>Pending</option>
                        <option value="REJECTED"  \${status === "REJECTED"  ? "selected" : ""}>Rejected</option>
                        <option value="CANCELLED" \${status === "CANCELLED" ? "selected" : ""}>Cancelled</option>
                    </select>
                </div>
                <button class="btn primary" id="attFilterBtn" style="width:100%;margin-top:10px;">Apply Filters</button>
            </div>
            <div class="panel">
                <h3>Registrations</h3>
                <p>Live from the booking table.</p>
                <table class="table" style="margin-top:15px;">
                    <thead><tr><th>Attendee</th><th>Event</th><th>Date</th><th>Attendees / Limit</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody id="attendeesBody"><tr><td colspan="6" style="text-align:center;color:var(--muted);padding:16px;">Loading...</td></tr></tbody>
                </table>
            </div>
        </div>`;

    const params = new URLSearchParams();
    if (search)  params.append("search",  search);
    if (eventId) params.append("eventId", eventId);
    if (status)  params.append("status",  status);

    const data = await fetchJSON(`api/attendees.php?${params}`);

    const evSel = el("attEventSel");
    if (evSel && data.success) {
        evSel.innerHTML = '<option value="">All events</option>' +
            (data.events || []).map(e =>
                `<option value="${e.EventID}" ${String(e.EventID) === String(eventId) ? "selected" : ""}>${e.Title}</option>`
            ).join("");
    }

    const tbody = el("attendeesBody");
    if (!tbody) return;
    if (!data.success || !data.bookings || data.bookings.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--muted);padding:16px;">No registrations found.</td></tr>`;
    } else {
        tbody.innerHTML = data.bookings.map(b => {
            const fillPct = b.CapacityLimit > 0 ? Math.round((b.TotalRegistered / b.CapacityLimit) * 100) : 0;
            const cls = fillPct >= 95 ? "danger" : fillPct >= 85 ? "warn" : "ok";
            const bookCls = b.BookingStatus === "APPROVED"  ? "ok"
                          : b.BookingStatus === "PENDING"   ? "warn"
                          : "danger";
            const safeName  = (b.AttendeeName || "").replace(/'/g, "");
            const safeEvent = (b.EventName    || "").replace(/'/g, "");
            const canRemove = b.BookingStatus !== "CANCELLED";
            return `<tr data-att-row="${b.BookingID}" style="transition:opacity .3s;">
                <td><b>${b.AttendeeName || "—"}</b><br><small style="color:var(--muted);">${b.AttendeeEmail || ""}</small></td>
                <td>${b.EventName || "—"}</td>
                <td>${b.EventDate || "—"}</td>
                <td><span class="badge ${cls}">${b.TotalRegistered} / ${b.CapacityLimit}</span></td>
                <td><span class="badge ${bookCls}">${b.BookingStatus || "—"}</span></td>
                <td>${canRemove
                    ? `<button class="btn small danger"
                        onclick="removeBooking(${b.BookingID}, '${safeName}', '${safeEvent}')">
                        ✕ Remove</button>`
                    : `<span style="color:var(--muted);font-size:12px;">Cancelled</span>`}
                </td>
            </tr>`;
        }).join("");
    }

    setTimeout(() => {
        el("attFilterBtn")?.addEventListener("click", () => {
            renderAttendeesTab(el("attSearch")?.value.trim() || "", el("attEventSel")?.value || "", el("attStatus")?.value || "");
        });
    }, 100);
}

window.removeBooking = async function(bookingID, attendeeName, eventName) {
    if (!confirm(`Remove ${attendeeName} from "${eventName}"?\nTheir booking will be cancelled.`)) return;

    // Capture filter values NOW before any DOM changes
    const currentSearch  = el("attSearch")?.value.trim()  || "";
    const currentEventId = el("attEventSel")?.value        || "";
    const currentStatus  = el("attStatus")?.value          || "";

    // Fade the row immediately so user sees instant feedback
    const row = document.querySelector(`[data-att-row="${bookingID}"]`);
    if (row) {
        row.style.transition = "opacity 0.2s, height 0.3s";
        row.style.opacity    = "0.3";
        row.querySelectorAll("button").forEach(b => b.disabled = true);
    }

    const result = await fetchJSON("api/remove-guest.php", {
        method:  "POST",
        headers: { "Content-Type": "application/json" },
        body:    JSON.stringify({ bookingID })
    });

    if (result.success) {
        showToast("Removed ✅", `${attendeeName} has been removed from "${eventName}".`);

        // Remove the row from the DOM immediately — no waiting, no flicker
        if (row) {
            row.style.opacity = "0";
            setTimeout(() => row.remove(), 250);
        }

        // Check if the table is now empty and show empty state
        setTimeout(() => {
            const tbody = el("attendeesBody");
            if (tbody && tbody.querySelectorAll("tr").length === 0) {
                tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--muted);padding:16px;">No registrations found.</td></tr>`;
            }
        }, 300);

        // Silently refresh capacity counts in background
        Promise.all([fetchJSON("api/events-list.php"), fetchJSON("api/kpi-stats.php")])
            .then(([evData]) => {
                if (evData.success && evData.events) {
                    dashboardEvents = evData.events;
                    allEventsCache  = evData.events;
                }
            });

    } else {
        showToast("Error", result.error || "Could not remove attendee.");
        // Restore row if the API call failed
        if (row) {
            row.style.opacity = "1";
            row.querySelectorAll("button").forEach(b => b.disabled = false);
        }
    }
};

// ==================== REPORTS TAB ====================
function loadReportsTab() { renderReportsTab(); }

async function renderReportsTab(startDate = "", endDate = "", venueId = "") {
    if (!startDate) startDate = new Date().toISOString().slice(0, 8) + "01";
    if (!endDate)   { const d = new Date(); d.setMonth(d.getMonth() + 1); d.setDate(0); endDate = d.toISOString().split("T")[0]; }

    getMain().innerHTML = `
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
            <div class="panel">
                <h3>Report Filters</h3>
                <div class="field"><label>Start Date</label><input type="date" id="repStart" value="${startDate}" /></div>
                <div class="field"><label>End Date</label><input type="date" id="repEnd" value="${endDate}" /></div>
                <div class="field"><label>Venue</label><select id="repVenue"><option value="">All Venues</option></select></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:15px;">
                    <button class="btn primary" id="repGenerateBtn">Generate Report</button>
                    <button class="btn" id="repExportBtn">⬇ Export Excel</button>
                </div>
            </div>
            <div class="panel">
                <h3>System Summary</h3>
                <div id="repSummary" style="display:grid;gap:12px;margin-top:15px;">
                    <div style="color:var(--muted);padding:16px;">Click Generate Report to load live data.</div>
                </div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-top:24px;">
            <div class="panel">
                <h3>Venue Utilization</h3>
                <table class="table" style="margin-top:15px;">
                    <thead><tr><th>Venue</th><th>Events</th><th>Total Registered</th><th>Utilization</th></tr></thead>
                    <tbody id="repVenueBody"><tr><td colspan="4" style="text-align:center;color:var(--muted);padding:16px;">Generate report to load.</td></tr></tbody>
                </table>
            </div>
            <div class="panel">
                <h3>🔔 Recent Activity Feed</h3>
                <p style="color:var(--muted);font-size:13px;margin-bottom:12px;">Latest actions across all events and registrations.</p>
                <div id="activityFeedBody" style="max-height:320px;overflow-y:auto;">
                    <div style="color:var(--muted);font-size:13px;padding:12px 0;text-align:center;">Loading activity...</div>
                </div>
            </div>
        </div>
        <div class="panel" style="margin-top:24px;">
            <h3>Capacity Analysis</h3>
            <table class="table">
                <thead><tr><th>Event</th><th>Venue</th><th>Date</th><th>Registered</th><th>Limit</th><th>Fill %</th><th>Status</th></tr></thead>
                <tbody id="repCapBody"><tr><td colspan="7" style="text-align:center;color:var(--muted);padding:16px;">Generate report to load.</td></tr></tbody>
            </table>
        </div>`;

    fetchJSON("api/venues.php").then(vd => {
        const sel = el("repVenue");
        if (sel && vd.success && vd.venues) {
            sel.innerHTML = '<option value="">All Venues</option>' +
                vd.venues.map(v => `<option value="${v.VenueID}" ${String(v.VenueID) === String(venueId) ? "selected" : ""}>${v.VenueName}</option>`).join("");
        }
    });

    setTimeout(() => {
        el("repGenerateBtn")?.addEventListener("click", generateReport);
        el("repExportBtn")  ?.addEventListener("click", exportExcel);
        generateReport();   // auto-load report on open
        loadActivityFeed(); // auto-load activity feed on open
    }, 100);
}

async function loadActivityFeed() {
    const panel = document.getElementById("activityFeedBody");
    if (!panel) return;

    const data = await fetchJSON("api/activity-feed.php");
    if (!data.success || !data.activities || data.activities.length === 0) {
        panel.innerHTML = `<div style="color:var(--muted);font-size:13px;padding:12px 0;text-align:center;">
            No recent activity to show.
        </div>`;
        return;
    }

    panel.innerHTML = data.activities.map((act, i) => {
        const isLast = i === data.activities.length - 1;
        return `<div style="
            display:flex;
            align-items:flex-start;
            gap:12px;
            padding:10px 0;
            ${!isLast ? "border-bottom:1px solid var(--glass-border);" : ""}
        ">
            <!-- Icon -->
            <div style="
                font-size:18px;
                flex-shrink:0;
                width:32px;
                height:32px;
                display:flex;
                align-items:center;
                justify-content:center;
                background:var(--glass);
                border-radius:50%;
            ">${act.Icon}</div>

            <!-- Text -->
            <div style="flex:1;min-width:0;">
                <div style="font-size:13px;color:var(--white);line-height:1.4;">
                    ${act.Description}
                </div>
                <div style="display:flex;align-items:center;gap:8px;margin-top:4px;">
                    <span class="badge ${act.BadgeClass}" style="font-size:10.5px;padding:2px 7px;">
                        ${act.BookingStatus}
                    </span>
                    <span style="font-size:11px;color:var(--muted);">${act.TimeAgo}</span>
                </div>
            </div>
        </div>`;
    }).join("");
}

async function generateReport() {
    const startDate = el("repStart")?.value || "", endDate = el("repEnd")?.value || "", venueId = el("repVenue")?.value || "";
    if (!startDate || !endDate) { showToast("Error", "Please select both start and end dates."); return; }
    const params = new URLSearchParams({ startDate, endDate });
    if (venueId) params.append("venueId", venueId);
    const data = await fetchJSON(`api/reports.php?${params}`);
    if (!data.success) { showToast("Error", data.error || "Failed to load report."); return; }

    const s = data.summary || {};
    const sum = el("repSummary");
    if (sum) sum.innerHTML = `
        <div style="background:#34d39930;padding:14px;border-radius:12px;color:#34d399;">Total Events: ${s.TotalEvents || 0}</div>
        <div style="background:#4ade8030;padding:14px;border-radius:12px;color:#4ade80;">Confirmed: ${s.ConfirmedEvents || 0} &nbsp;|&nbsp; Draft: ${s.DraftEvents || 0}</div>
        <div style="background:#38bdf830;padding:14px;border-radius:12px;color:#38bdf8;">Total Bookings: ${s.TotalBookings || 0}</div>
        <div style="background:#fbbf2430;padding:14px;border-radius:12px;color:#fbbf24;">Total Attendee Capacity: ${s.TotalCapacity || 0}</div>`;

    const vBody = el("repVenueBody");
    if (vBody) vBody.innerHTML = (data.venueUtil || []).length === 0
        ? `<tr><td colspan="4" style="text-align:center;color:var(--muted);padding:16px;">No data for this period.</td></tr>`
        : data.venueUtil.map(v => `<tr>
            <td>${v.VenueName}</td><td>${v.BookedSlots}</td><td>${v.TotalRegistered || 0}</td>
            <td><span class="badge ${v.UtilStatus}">${v.UtilStatus === "danger" ? "High" : v.UtilStatus === "warn" ? "Medium" : "Low"}</span></td>
        </tr>`).join("");

    // Load recent activity feed alongside the report
    loadActivityFeed();

    const capBody = el("repCapBody");
    if (capBody) capBody.innerHTML = (data.capacity || []).length === 0
        ? `<tr><td colspan="7" style="text-align:center;color:var(--muted);padding:16px;">No events in this period.</td></tr>`
        : data.capacity.map(ev => `<tr>
            <td><b>${ev.EventName}</b></td><td>${ev.VenueName || "—"}</td><td>${ev.EventDate || "—"}</td>
            <td>${ev.Registered}</td><td>${ev.CapacityLimit}</td><td>${ev.FillPct || 0}%</td>
            <td><span class="badge ${ev.CapStatus}">${ev.CapStatus === "danger" ? "Full" : ev.CapStatus === "warn" ? "Near capacity" : "OK"}</span></td>
        </tr>`).join("");

    showToast("Report Ready", `Showing data from ${startDate} to ${endDate}.`);
}

function exportExcel() {
    const startDate = el("repStart")?.value || "", endDate = el("repEnd")?.value || "", venueId = el("repVenue")?.value || "";
    if (!startDate || !endDate) { showToast("Error", "Select dates before exporting."); return; }
    const params = new URLSearchParams({ startDate, endDate });
    if (venueId) params.append("venueId", venueId);
    window.location.href = `api/export.php?${params}`;
    showToast("Exporting ⬇", "Your Excel file will download shortly.");
}

// ==================== MAIN RENDER ====================
function renderMain() {
    const canCreate = window.canCreateEvent === true;
    const canManage = window.canManage      === true;

    // Guard: redirect unauthorized tab access to dashboard
    if (state.tab === "resources" && !canCreate) { state.tab = "dashboard"; }
    if (state.tab === "attendees" && !canManage) { state.tab = "dashboard"; }
    if (state.tab === "reports"   && !canManage) { state.tab = "dashboard"; }

    if (state.tab === "dashboard") { loadDashboardData(); return; }
    if (state.tab === "events")    { loadEventsTab();     return; }
    if (state.tab === "schedule")  { loadScheduleTab();   return; }
    if (state.tab === "resources") { loadResourcesTab();  return; }
    if (state.tab === "attendees") { loadAttendeesTab();  return; }
    if (state.tab === "reports")   { loadReportsTab();    return; }
    getMain().innerHTML = `<div class="panel"><h3>${state.tab}</h3><p>Coming soon...</p></div>`;
}

// ==================== INIT ====================
function init() {
    setHeaderForRole();
    document.querySelectorAll(".tab").forEach(t => { t.addEventListener("click", () => setActiveTab(t.dataset.tab)); });
    document.querySelectorAll(".chip").forEach(chip => {
        chip.addEventListener("click", () => {
            document.querySelectorAll(".chip").forEach(c => c.classList.remove("active"));
            chip.classList.add("active");
            if (state.tab !== "events") setActiveTab("events");
            else applyQuickFilters();
        });
    });
    el("venueFilter")?.addEventListener("change", () => { if (state.tab === "events") applyQuickFilters(); });
    el("dateFilter") ?.addEventListener("change", () => { if (state.tab === "events") applyQuickFilters(); });
    el("helpBtn")    ?.addEventListener("click", showTipsModal);
    renderMain();
}

document.addEventListener("DOMContentLoaded", init);