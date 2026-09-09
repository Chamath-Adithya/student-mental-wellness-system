/**
 * Student Mental Wellness Check-in System
 * Production Client-Side Logic:
 * - Bilingual Support (Sinhala & English)
 * - Vector-Card Daily Mood Logging
 * - PHQ-9 & GAD-7 Assessment Engine
 * - 4-7-8 Breathing & 5-4-3-2-1 Grounding Modals
 * - Counseling Appointment Booking
 * - Offline Ambient Audio Synthesizer (Web Audio API)
 * - Compassionate AI Wellness Assistant
 * - Chart.js Score Trajectory Visualizer
 */

/* ==========================================================================
   1. DICTIONARIES & CLINICAL ASSETS
   ========================================================================== */
const APP_DATA = {
    si: {
        brandTitle: "Sansun",
        brandSub: "ශිෂ්‍ය මානසික සුවතා පද්ධතිය",
        navAssessment: "ස්වයං ඇගයීම",
        navMood: "දෛනික මනෝභාවය",
        navCounseling: "උපදේශනය",
        navHistory: "ප්‍රගතිය",
        navDirectory: "සායන නාමාවලිය",
        heroTitle: "ඔබේ මානසික සුවතාවය වෙනුවෙන් සුරක්ෂිත ඉඩක්",
        heroDesc: "විභාග පීඩනය, අධ්‍යයන තෙහෙට්ටුව සහ මානසික ආතතිය හඳුනාගෙන, මනස සන්සුන් කරගැනීමට අවශ්‍ය වෘත්තීය මගපෙන්වීම් සහ උපදේශන පහසුකම් මෙහි ඇතුළත් වේ.",
        heroBtn: "ස්වයං ඇගයීම ආරම්භ කරන්න",
        heroMoodBtn: "දෛනික සටහන",
        cardGreeting: "ආයුබෝවන්, ",
        moodHeading: "දෛනික මනෝභාවය සටහන් කරන්න",
        moodSubtitle: "අධ්‍යයන වාරය පුරා ඔබේ මානසික මට්ටම් වෙනස්වන ආකාරය නිරීක්ෂණය කිරීමට අද දිනට අදාළ හැඟීම තෝරන්න.",
        mTitle1: "ප්‍රබෝධමත්", mDesc1: "ශක්තිමත් & ක්‍රියාශීලී",
        mTitle2: "සන්සුන්", mDesc2: "සාමකාමී & පාලිත",
        mTitle3: "වෙහෙසයි", mDesc3: "අඩු ශක්තිය / තෙහෙට්ටුව",
        mTitle4: "පීඩිතයි", mDesc4: "කනස්සල්ලෙන් / බියෙන්",
        lblMoodNote: "අද දින පිළිබඳ සටහනක් (Optional)",
        btnSaveMood: "දෛනික සටහන සුරකින්න",
        assessHeading: "සම්මත මානසික සෞඛ්‍ය ස්වයං ඇගයීම",
        assessSubtitle: "ජාත්‍යන්තරව පිළිගත් PHQ-9 සහ GAD-7 පරිමාණ උපයෝගී කරගනිමින් විෂාදය සහ කාංසාව පිළිබඳ මූලික තක්සේරුවක් ලබාගන්න.",
        tabPhq: "PHQ-9 (විෂාදය)",
        tabGad: "GAD-7 (කාංසාව)",
        chartHeading: "ඔබේ ලකුණු ප්‍රගතිය සහ ප්‍රවණතා",
        chartSubtitle: "පසුගිය ඇගයීම් ලකුණු විශ්ලේෂණය කර මානසික යහපැවැත්ම සංසන්දනය කරන්න.",
        dirHeading: "දිස්ත්‍රික්ක අනුව මානසික සෞඛ්‍ය සායන නාමාවලිය",
        dirSubtitle: "ශ්‍රී ලංකාවේ ප්‍රධාන රජයේ රෝහල්වල ක්‍රියාත්මක මනෝ වෛද්‍ය ඒකක පිළිබඳ සෘජු තොරතුරු.",
        thDist: "දිස්ත්‍රික්කය",
        thHosp: "රෝහල / විශේෂිත මධ්‍යස්ථානය",
        thTel: "සෘජු දුරකථන අංකය",
        helpTitle: "පැය 24 පුරා ක්‍රියාත්මක හදිසි උපකාරක සේවා",
        hl1: "ජාතික මානසික සෞඛ්‍ය විද්‍යායතනය",
        hl2: "CCC Line අර්බුද කළමනාකරණය",
        hl3: "ශ්‍රී ලංකා සුමිත්‍රයෝ",
        breathSub: "හෘද ස්පන්දනය පාලනය කර ස්නායු පද්ධතිය සන්සුන් කරන සම්මත හුස්ම ගැනීමේ ක්‍රමයකි.",
        breathInhale: "හුස්ම ගන්න (තත්. 4)",
        breathHold: "රඳවා ගන්න (තත්. 7)",
        breathExhale: "හෙමින් පිටකරන්න (තත්. 8)",
        breathReady: "සූදානම් වන්න...",
        chatTitle: "සුවතා AI සහායක",
        chatWelcome: "ආයුබෝවන්! මම ඔබේ ශිෂ්‍ය සුවතා සහායකයා වෙමි. ඔබට ඇති ඕනෑම අධ්‍යාපනික පීඩනයක්, කනස්සල්ලක් හෝ ප්‍රශ්නයක් මා සමඟ බෙදාගත හැක. මා ඔබට උදවු කරන්නේ කෙසේද?",
        options: ["කොහෙත්ම නැත (0)", "දින කිහිපයක් (1)", "සතියකට වැඩි දින ගණනක් (2)", "දිනපතාම වාගේ (3)"],
        phq9: [
            "1. වැඩ කටයුතු කෙරෙහි ඇති උනන්දුව හෝ සතුට අඩුවීම",
            "2. කනස්සල්ලෙන්, මානසිකව වැටී හෝ බලාපොරොත්තු රහිතව පසුවීම",
            "3. නින්ද යෑමේ අපහසුව, නින්දෙන් අවදි වීම හෝ අධික ලෙස නිදාගැනීම",
            "4. අධික වෙහෙසකාරී බව හෝ ශක්තිය හීන වීම",
            "5. කෑම අරුචිය හෝ අධික ලෙස ආහාර ගැනීම",
            "6. තමා ගැන අප්‍රසාදයක් දැනීම, අසාර්ථක අයෙකු ලෙස සිතීම",
            "7. පත්තර කියවීම හෝ පාඩම් කිරීම වැනි දේ කෙරෙහි අවධානය යොමු කිරීමේ අපහසුව",
            "8. අන් අයට පෙනෙන තරම් මන්දගාමී වීම හෝ අධික නොසන්සුන්කම",
            "9. මියයෑම වඩා හොඳයැයි සිතීම හෝ තමාටම හානි කරගැනීමට සිතීම"
        ],
        gad7: [
            "1. නොසන්සුන්බව, කලබලකාරී බව හෝ බියකරු හැඟීමක් දැනීම",
            "2. අනවශ්‍ය කරදර වීම් නතර කිරීමට හෝ පාලනය කිරීමට නොහැකි වීම",
            "3. විවිධ දේවල් ගැන අධික ලෙස වද වීම",
            "4. සැහැල්ලුවෙන් විවේකීව සිටීමට අපහසු වීම",
            "5. එක තැන සිටීමට නොහැකි තරම් නොසන්සුන් වීම",
            "6. ඉතා ඉක්මනින් කෝප වීම හෝ නොඉවසිලිමත් වීම",
            "7. නරක යමක් සිදුවනු ඇතැයි යන බියෙන් පසුවීම"
        ]
    },
    en: {
        brandTitle: "Sansun",
        brandSub: "Student Mental Wellness",
        navAssessment: "Assessment",
        navMood: "Mood Log",
        navCounseling: "Counseling",
        navHistory: "History",
        navDirectory: "Clinics",
        heroTitle: "A Safe Space for Your Mental Well-being",
        heroDesc: "Identify academic stress early, log emotional patterns, practice guided breathing, and access confidential student counseling support.",
        heroBtn: "Take Self-Check Assessment",
        heroMoodBtn: "Daily Reflection",
        cardGreeting: "Welcome, ",
        moodHeading: "Daily Emotional Reflection",
        moodSubtitle: "Select your dominant emotional state today to log patterns over the academic semester.",
        mTitle1: "Thriving", mDesc1: "Energized & Motivated",
        mTitle2: "Balanced", mDesc2: "Calm & In Control",
        mTitle3: "Fatigued", mDesc3: "Low Energy / Drained",
        mTitle4: "Distressed", mDesc4: "Anxious / Overwhelmed",
        lblMoodNote: "Reflections or Notes (Optional)",
        btnSaveMood: "Save Daily Reflection",
        assessHeading: "Standardized Mental Health Self-Check",
        assessSubtitle: "Internationally validated PHQ-9 (Depression) and GAD-7 (Anxiety) screening modules.",
        tabPhq: "PHQ-9 (Depression Scale)",
        tabGad: "GAD-7 (Anxiety Scale)",
        chartHeading: "Personal Score History & Trends",
        chartSubtitle: "Track changes in depression and anxiety indicators across consecutive check-ins.",
        dirHeading: "Institutional & National Healthcare Directory",
        dirSubtitle: "Direct contacts to psychiatric and mental healthcare clinics across Sri Lanka.",
        thDist: "District",
        thHosp: "Hospital / Specialized Center",
        thTel: "Direct Contact",
        helpTitle: "Emergency 24/7 Support Lines",
        hl1: "National Mental Health Institute",
        hl2: "CCC Line Crisis Support",
        hl3: "Sri Lanka Sumithrayo",
        breathSub: "A clinically proven rhythm to reduce heart rate and trigger the parasympathetic nervous system.",
        breathInhale: "Inhale (4s)",
        breathHold: "Hold (7s)",
        breathExhale: "Exhale (8s)",
        breathReady: "Get Ready...",
        chatTitle: "Wellness AI Assistant",
        chatWelcome: "Hello! I am your student wellness assistant. Feel free to share whatever is on your mind. How can I assist you today?",
        options: ["Not at all (0)", "Several days (1)", "More than half the days (2)", "Nearly every day (3)"],
        phq9: [
            "1. Little interest or pleasure in doing things",
            "2. Feeling down, depressed, or hopeless",
            "3. Trouble falling or staying asleep, or sleeping too much",
            "4. Feeling tired or having little energy",
            "5. Poor appetite or overeating",
            "6. Feeling bad about yourself, feeling like a failure",
            "7. Trouble concentrating on things, such as reading or studying",
            "8. Moving or speaking noticeably slowly, or being restless",
            "9. Thoughts that you would be better off dead, or hurting yourself"
        ],
        gad7: [
            "1. Feeling nervous, anxious, or on edge",
            "2. Not being able to stop or control worrying",
            "3. Worrying too much about different things",
            "4. Trouble relaxing",
            "5. Being so restless that it's hard to sit still",
            "6. Becoming easily annoyed or irritable",
            "7. Feeling afraid, as if something awful might happen"
        ]
    }
};

const CLINIC_DIRECTORY = [
    { district: "Colombo (කොළඹ)", hospital: "National Institute of Mental Health (NIMH), Angoda", phone: "011 2578234" },
    { district: "Colombo (කොළඹ)", hospital: "National Hospital of Sri Lanka (Psychiatry Unit)", phone: "011 2691111" },
    { district: "Kandy (මහනුවර)", hospital: "Peradeniya Teaching Hospital (Mental Health Unit)", phone: "081 2388000" },
    { district: "Galle (ගාල්ල)", hospital: "Karapitiya Teaching Hospital", phone: "091 2232250" },
    { district: "Jaffna (යාපනය)", hospital: "Teaching Hospital, Jaffna", phone: "021 2222261" },
    { district: "Kurunegala (කුරුණෑගල)", hospital: "Teaching Hospital, Kurunegala", phone: "037 2222261" },
    { district: "Anuradhapura (අනුරාධපුර)", hospital: "Teaching Hospital, Anuradhapura", phone: "025 2222261" }
];

const GROUNDING_STEPS = {
    si: [
        { count: 5, icon: "fa-eye", text: "<strong>5 - ඔබ අවට පෙනෙන දේවල් 5ක්</strong> දෙස හොඳින් අවධානය යොමු කරන්න." },
        { count: 4, icon: "fa-hand", text: "<strong>4 - ඔබට ඇල්ලිය හැකි දේවල් 4ක්</strong> ස්පර්ශ කර බලන්න." },
        { count: 3, icon: "fa-ear-listen", text: "<strong>3 - පරිසරයෙන් ඇසෙන ශබ්ද 3කට</strong> සවන් දෙන්න." },
        { count: 2, icon: "fa-wind", text: "<strong>2 - දැනෙන සුවඳ වර්ග 2ක්</strong> හඳුනාගන්න." },
        { count: 1, icon: "fa-utensils", text: "<strong>1 - ඔබේ මුවට දැනෙන රසයක්</strong> කෙරෙහි සිත යොමු කරන්න." }
    ],
    en: [
        { count: 5, icon: "fa-eye", text: "<strong>5 - Look around at 5 things</strong> you can see right now." },
        { count: 4, icon: "fa-hand", text: "<strong>4 - Touch 4 physical objects</strong> within your reach." },
        { count: 3, icon: "fa-ear-listen", text: "<strong>3 - Listen carefully for 3 distinct sounds</strong> around you." },
        { count: 2, icon: "fa-wind", text: "<strong>2 - Notice 2 scents or aromas</strong> in the air." },
        { count: 1, icon: "fa-utensils", text: "<strong>1 - Focus on 1 taste</strong> in your mouth." }
    ]
};

/* ==========================================================================
   2. APP STATE
   ========================================================================== */
let currentLang = 'en'; // default English, toggleable
let currentTestType = 'phq9';
let currentQuestionIndex = 0;
let assessmentAnswers = {};
let selectedVectorMood = { code: 'balanced', icon: 'fa-seedling', label: 'Balanced' };
let breathingTimer = null;
let trendChartInstance = null;

// Synthesizer Audio State
let audioCtx = null;
let noiseNode = null;
let gainNode = null;
let isAudioPlaying = false;

/* ==========================================================================
   3. INITIALIZATION
   ========================================================================== */
document.addEventListener('DOMContentLoaded', () => {
    // Theme setup
    const savedTheme = localStorage.getItem('sansun_theme');
    if (savedTheme === 'dark') {
        document.body.setAttribute('data-theme', 'dark');
    }

    // Language setup
    const savedLang = localStorage.getItem('sansun_lang');
    if (savedLang === 'si' || savedLang === 'en') {
        currentLang = savedLang;
    }

    applyLanguageStrings();
    renderClinicsTable();
    renderGroundingContent();
    renderAssessmentQuestion();
    loadMoodLogs();
    initTrendChart();
});

/* ==========================================================================
   4. LANGUAGE & THEME CONTROLS
   ========================================================================== */
function toggleLanguage() {
    currentLang = (currentLang === 'si') ? 'en' : 'si';
    localStorage.setItem('sansun_lang', currentLang);
    applyLanguageStrings();
    renderClinicsTable();
    renderGroundingContent();
    renderAssessmentQuestion();
}

function toggleThemeMode() {
    const isDark = document.body.getAttribute('data-theme') === 'dark';
    if (isDark) {
        document.body.removeAttribute('data-theme');
        localStorage.setItem('sansun_theme', 'light');
    } else {
        document.body.setAttribute('data-theme', 'dark');
        localStorage.setItem('sansun_theme', 'dark');
    }
}

function applyLanguageStrings() {
    const d = APP_DATA[currentLang];
    const update = (id, text) => {
        const el = document.getElementById(id);
        if (el) el.innerText = text;
    };

    update('txtBrand', d.brandTitle);
    update('txtBrandSub', d.brandSub);
    update('navAssessment', d.navAssessment);
    update('navMood', d.navMood);
    update('navCounseling', d.navCounseling);
    update('navHistory', d.navHistory);
    update('navDirectory', d.navDirectory);

    update('heroHeading', d.heroTitle);
    update('heroDescription', d.heroDesc);
    update('heroBtn', d.heroBtn);
    update('heroMoodBtn', d.heroMoodBtn);
    
    const greetingEl = document.getElementById('cardGreeting');
    if (greetingEl && typeof LOGGED_IN_STUDENT_NAME !== 'undefined') {
        greetingEl.innerText = d.cardGreeting + LOGGED_IN_STUDENT_NAME;
    }

    update('moodHeading', d.moodHeading);
    update('moodSubtitle', d.moodSubtitle);
    update('mTitle1', d.mTitle1); update('mDesc1', d.mDesc1);
    update('mTitle2', d.mTitle2); update('mDesc2', d.mDesc2);
    update('mTitle3', d.mTitle3); update('mDesc3', d.mDesc3);
    update('mTitle4', d.mTitle4); update('mDesc4', d.mDesc4);
    update('lblMoodNote', d.lblMoodNote);
    update('btnSaveMood', d.btnSaveMood);

    update('assessHeading', d.assessHeading);
    update('assessSubtitle', d.assessSubtitle);
    update('tabPhq', d.tabPhq);
    update('tabGad', d.tabGad);

    update('chartHeading', d.chartHeading);
    update('chartSubtitle', d.chartSubtitle);

    update('dirHeading', d.dirHeading);
    update('dirSubtitle', d.dirSubtitle);
    update('thDist', d.thDist);
    update('thHosp', d.thHosp);
    update('thTel', d.thTel);
    update('helpTitle', d.helpTitle);
    update('hl1', d.hl1);
    update('hl2', d.hl2);
    update('hl3', d.hl3);

    update('breathSub', d.breathSub);
    update('chatHeaderTitle', d.chatTitle);

    const dropLangTxt = document.getElementById('dropLangTxt');
    if (dropLangTxt) {
        dropLangTxt.innerText = (currentLang === 'si') ? 'English' : 'සිංහල';
    }

    const audioText = document.getElementById('audioText');
    if (audioText && !isAudioPlaying) {
        audioText.innerText = (currentLang === 'si') ? 'සන්සුන් වැස්ස' : 'Calm Rain';
    }
}

/* ==========================================================================
   5. VECTOR MOOD LOGGING MODULE
   ========================================================================== */
function selectVectorMood(code, icon, label, element) {
    selectedVectorMood = { code, icon, label };
    document.querySelectorAll('.mood-card-item').forEach(card => card.classList.remove('active'));
    if (element) {
        element.classList.add('active');
    }
}

async function submitMoodLog() {
    const note = document.getElementById('moodNotes')?.value || '';
    const statusInd = document.getElementById('moodStatusIndicator');
    
    if (statusInd) statusInd.innerText = currentLang === 'si' ? "සුරකිමින් පවතී..." : "Saving reflection...";

    try {
        const res = await fetch(SITE_ROOT + '/api/mood.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                mood_code: selectedVectorMood.code,
                mood_icon: selectedVectorMood.icon,
                mood_label: selectedVectorMood.label,
                note: note
            })
        });
        const data = await res.json();
        if (data.success) {
            if (statusInd) {
                statusInd.style.color = 'var(--success)';
                statusInd.innerText = currentLang === 'si' ? "✓ සටහන සාර්ථකව සුරකින ලදී." : "✓ State logged successfully.";
            }
            const noteInput = document.getElementById('moodNotes');
            if (noteInput) noteInput.value = '';
            loadMoodLogs();
        } else {
            if (statusInd) statusInd.innerText = "Error: " + (data.message || 'Could not save.');
        }
    } catch (e) {
        if (statusInd) statusInd.innerText = "Network error: " + e.message;
    }
}

async function loadMoodLogs() {
    const stream = document.getElementById('moodRecentStream');
    if (!stream) return;

    try {
        const res = await fetch(SITE_ROOT + '/api/mood.php');
        const data = await res.json();
        if (data.logs && data.logs.length > 0) {
            let html = `<div style="font-weight:700; color:var(--primary); margin-bottom:8px;">Recent Emotional Check-ins:</div><div style="display:flex; flex-direction:column; gap:8px;">`;
            data.logs.slice(0, 4).forEach(item => {
                const iconClass = item.mood_icon || 'fa-seedling';
                html += `
                    <div style="display:flex; justify-content:space-between; align-items:center; background:var(--bg); padding:10px 14px; border-radius:10px; border:1px solid var(--border);">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <i class="fa-solid ${iconClass}" style="color:var(--primary); font-size:1.1rem;"></i>
                            <div>
                                <strong style="font-size:0.875rem;">${item.mood_label}</strong>
                                ${item.note ? `<p style="font-size:0.775rem; color:var(--text-muted); margin-top:2px;">"${item.note}"</p>` : ''}
                            </div>
                        </div>
                        <small style="color:var(--text-muted); font-size:0.75rem;">${item.created_at ? item.created_at.substring(0, 16) : ''}</small>
                    </div>
                `;
            });
            html += `</div>`;
            stream.innerHTML = html;
        }
    } catch (e) {
        console.error("Failed to load mood logs", e);
    }
}

/* ==========================================================================
   6. CLINICAL ASSESSMENT WIZARD (PHQ-9 & GAD-7)
   ========================================================================== */
function selectAssessmentTab(testType) {
    currentTestType = testType;
    currentQuestionIndex = 0;
    assessmentAnswers = {};

    document.getElementById('tabPhq')?.classList.toggle('active', testType === 'phq9');
    document.getElementById('tabGad')?.classList.toggle('active', testType === 'gad7');

    const resultView = document.getElementById('assessmentResultView');
    const wizardForm = document.getElementById('assessmentWizard');
    if (resultView) resultView.style.display = 'none';
    if (wizardForm) wizardForm.style.display = 'block';

    renderAssessmentQuestion();
}

function renderAssessmentQuestion() {
    const questions = APP_DATA[currentLang][currentTestType];
    const total = questions.length;
    const progress = Math.round(((currentQuestionIndex) / total) * 100);

    const progressFill = document.getElementById('assessmentProgressBar');
    if (progressFill) progressFill.style.width = `${progress}%`;

    const indicator = document.getElementById('qStepIndicator');
    if (indicator) {
        indicator.innerText = `${currentLang === 'si' ? 'ප්‍රශ්න' : 'Question'} ${currentQuestionIndex + 1} ${currentLang === 'si' ? 'න්' : 'of'} ${total}`;
    }

    const stmtEl = document.getElementById('qStatement');
    if (stmtEl) stmtEl.innerText = questions[currentQuestionIndex];

    const optionsList = document.getElementById('optionsContainer');
    if (optionsList) {
        const opts = APP_DATA[currentLang].options;
        let html = '';
        opts.forEach((optText, score) => {
            const isSelected = (assessmentAnswers[currentQuestionIndex] === score) ? 'selected' : '';
            html += `
                <div class="option-choice ${isSelected}" onclick="selectOption(${score})">
                    <span>${optText}</span>
                    <i class="fa-solid ${isSelected ? 'fa-circle-check' : 'fa-circle'}" style="color:${isSelected ? 'var(--primary)' : 'var(--border)'};"></i>
                </div>
            `;
        });
        optionsList.innerHTML = html;
    }

    const prevBtn = document.getElementById('btnPrevQ');
    if (prevBtn) prevBtn.style.display = (currentQuestionIndex > 0) ? 'inline-flex' : 'none';

    const nextBtn = document.getElementById('btnNextQ');
    if (nextBtn) {
        if (currentQuestionIndex === total - 1) {
            nextBtn.innerHTML = `${currentLang === 'si' ? 'අවසන් කරන්න' : 'Complete Assessment'} <i class="fa-solid fa-check-double"></i>`;
        } else {
            nextBtn.innerHTML = `${currentLang === 'si' ? 'ඉදිරියට' : 'Next'} <i class="fa-solid fa-arrow-right"></i>`;
        }
    }
}

function selectOption(score) {
    assessmentAnswers[currentQuestionIndex] = score;
    renderAssessmentQuestion();
}

function moveQuestion(direction) {
    const questions = APP_DATA[currentLang][currentTestType];
    if (direction === 1 && assessmentAnswers[currentQuestionIndex] === undefined) {
        alert(currentLang === 'si' ? "කරුණාකර පිළිතුරක් තෝරන්න." : "Please select one of the options above to proceed.");
        return;
    }

    currentQuestionIndex += direction;
    if (currentQuestionIndex >= questions.length) {
        completeAssessment();
    } else {
        renderAssessmentQuestion();
    }
}

async function completeAssessment() {
    let totalScore = 0;
    Object.values(assessmentAnswers).forEach(val => totalScore += Number(val));

    try {
        const res = await fetch(SITE_ROOT + '/api/assessment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                test_type: currentTestType,
                score: totalScore,
                answers: assessmentAnswers
            })
        });
        const data = await res.json();
        renderAssessmentResult(data);
        initTrendChart();
    } catch (e) {
        alert("Could not save assessment: " + e.message);
    }
}

function renderAssessmentResult(res) {
    const wizard = document.getElementById('assessmentWizard');
    const resultView = document.getElementById('assessmentResultView');
    if (wizard) wizard.style.display = 'none';
    if (!resultView) return;

    resultView.style.display = 'block';
    const isHighRisk = res.is_high_risk;

    const alertHtml = isHighRisk ? `
        <div style="background:#fee2e2; border-left:4px solid #ef4444; color:#991b1b; padding:16px; border-radius:10px; margin:20px 0; text-align:left;">
            <strong><i class="fa-solid fa-triangle-exclamation"></i> Immediate Support Advised:</strong>
            <p style="font-size:0.875rem; margin-top:4px;">Your screening score indicates significant emotional distress. Please consider speaking with an institutional counselor or call the toll-free 24/7 National Mental Health Line at <strong>1926</strong>.</p>
        </div>
    ` : '';

    resultView.innerHTML = `
        <div style="width:64px; height:64px; border-radius:50%; background:${isHighRisk ? '#fee2e2' : '#dcfce7'}; color:${isHighRisk ? '#dc2626' : '#15803d'}; display:flex; align-items:center; justify-content:center; font-size:1.8rem; margin:0 auto 16px auto;">
            <i class="fa-solid ${isHighRisk ? 'fa-triangle-exclamation' : 'fa-circle-check'}"></i>
        </div>
        <h3 style="font-size:1.35rem; color:var(--text-main);">${currentTestType.toUpperCase()} Screening Completed</h3>
        <p style="color:var(--text-muted); font-size:0.875rem;">Confidential Self-Check Results</p>

        <div style="background:var(--bg); border:1px solid var(--border); border-radius:14px; padding:22px; margin:20px 0;">
            <div style="font-size:2.8rem; font-weight:800; color:var(--primary); line-height:1;">${res.score}</div>
            <div style="font-size:1.15rem; font-weight:700; color:var(--text-main); margin-top:8px;">${res.severity}</div>
            <p style="font-size:0.875rem; color:var(--text-muted); margin-top:8px; max-width:540px; margin-left:auto; margin-right:auto;">
                ${res.recommendation}
            </p>
        </div>

        ${alertHtml}

        <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-top:20px;">
            <button class="btn-primary-action" onclick="selectAssessmentTab('${currentTestType}')">
                <i class="fa-solid fa-rotate-right"></i> Retake Check-in
            </button>
            <button class="btn-outline-action" onclick="openCounselingModal()">
                <i class="fa-solid fa-user-doctor"></i> Schedule Counseling Session
            </button>
        </div>
    `;
}

/* ==========================================================================
   7. INTERACTIVE 4-7-8 BREATHING EXERCISE MODAL
   ========================================================================== */
function openBreathingModal() {
    const modal = document.getElementById('modalBreath');
    if (modal) {
        modal.style.display = 'flex';
        startBreathingCycle();
    }
}

function closeBreathingModal() {
    const modal = document.getElementById('modalBreath');
    if (modal) modal.style.display = 'none';
    if (breathingTimer) clearTimeout(breathingTimer);
    const orb = document.getElementById('breathOrb');
    if (orb) {
        orb.className = 'breath-orb';
        orb.innerText = APP_DATA[currentLang].breathReady;
    }
}

function startBreathingCycle() {
    const orb = document.getElementById('breathOrb');
    const inst = document.getElementById('breathInstruction');
    const d = APP_DATA[currentLang];
    if (!orb) return;

    // Step 1: Inhale 4s
    orb.className = 'breath-orb expand';
    orb.innerText = d.breathInhale;
    if (inst) inst.innerText = currentLang === 'si' ? "තත්පර 4ක් තදින් හුස්ම ඉහළට ගන්න..." : "Inhale deeply through your nose for 4 seconds...";

    breathingTimer = setTimeout(() => {
        // Step 2: Hold 7s
        orb.className = 'breath-orb hold';
        orb.innerText = d.breathHold;
        if (inst) inst.innerText = currentLang === 'si' ? "තත්පර 7ක් හුස්ම රඳවා ගන්න..." : "Hold your breath calmly for 7 seconds...";

        breathingTimer = setTimeout(() => {
            // Step 3: Exhale 8s
            orb.className = 'breath-orb shrink';
            orb.innerText = d.breathExhale;
            if (inst) inst.innerText = currentLang === 'si' ? "තත්පර 8ක් පුරා හෙමින් හුස්ම පහතට පිටකරන්න..." : "Exhale completely and gently through your mouth for 8 seconds...";

            breathingTimer = setTimeout(() => {
                startBreathingCycle();
            }, 8000);
        }, 7000);
    }, 4000);
}

/* ==========================================================================
   8. INTERACTIVE 5-4-3-2-1 GROUNDING TECHNIQUE MODAL
   ========================================================================== */
function openGroundingModal() {
    const modal = document.getElementById('modalGround');
    if (modal) modal.style.display = 'flex';
}

function closeGroundingModal() {
    const modal = document.getElementById('modalGround');
    if (modal) modal.style.display = 'none';
}

function renderGroundingContent() {
    const container = document.getElementById('groundingItems');
    if (!container) return;

    const list = GROUNDING_STEPS[currentLang];
    let html = '';
    list.forEach(item => {
        html += `
            <div style="display:flex; align-items:center; gap:12px; background:var(--bg); border:1px solid var(--border); padding:12px 16px; border-radius:12px;">
                <div style="width:34px; height:34px; border-radius:8px; background:var(--primary-light); color:var(--primary); display:flex; align-items:center; justify-content:center; font-weight:700;">
                    <i class="fa-solid ${item.icon}"></i>
                </div>
                <div>${item.text}</div>
            </div>
        `;
    });
    container.innerHTML = html;
}

/* ==========================================================================
   9. COUNSELING APPOINTMENT BOOKING MODAL
   ========================================================================== */
function openCounselingModal() {
    const modal = document.getElementById('modalCounsel');
    if (modal) modal.style.display = 'flex';
}

function closeCounselingModal() {
    const modal = document.getElementById('modalCounsel');
    if (modal) modal.style.display = 'none';
}

function toggleCounselorPrivacy(mode) {
    const field = document.getElementById('counselNameField');
    if (field) {
        field.style.display = (mode === 'anonymous') ? 'none' : 'block';
    }
}

async function submitCounselingBooking(event) {
    event.preventDefault();
    const privacy = document.getElementById('counselPrivacy')?.value || 'named';
    const studentName = document.getElementById('counselName')?.value || 'Anonymous Student';
    const sessionMode = document.getElementById('counselMode')?.value || 'online';
    const sessionDate = document.getElementById('counselDate')?.value || '';
    const notes = document.getElementById('counselNotes')?.value || '';

    try {
        const res = await fetch(SITE_ROOT + '/api/counseling.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                is_anonymous: (privacy === 'anonymous') ? 1 : 0,
                student_name: (privacy === 'anonymous') ? 'Anonymous' : studentName,
                preferred_mode: sessionMode,
                preferred_date: sessionDate,
                reason: notes
            })
        });
        const data = await res.json();
        if (data.success) {
            alert(currentLang === 'si' ? "ඔබගේ උපදේශන ඉල්ලීම සාර්ථකව යොමු විය. උපදේශකවරයා ළඟදීම ඔබව සම්බන්ධ කරගනු ඇත." : "Counseling appointment request submitted successfully. The counselor will follow up with you.");
            closeCounselingModal();
        } else {
            alert("Error: " + (data.message || 'Could not submit request.'));
        }
    } catch (e) {
        alert("Booking error: " + e.message);
    }
}

/* ==========================================================================
   10. AMBIENT CALMING SOUND (WEB AUDIO API SYNTHESIZER)
   ========================================================================== */
function toggleAudio() {
    const icon = document.getElementById('audioIcon');
    const text = document.getElementById('audioText');
    const btn = document.getElementById('btnAudio');

    if (!isAudioPlaying) {
        startAmbientNoise();
        isAudioPlaying = true;
        if (icon) icon.className = "fa-solid fa-pause";
        if (text) text.innerText = (currentLang === 'si') ? "වෙහෙස නිවන්න" : "Pause Rain";
        if (btn) btn.classList.add('playing');
    } else {
        stopAmbientNoise();
        isAudioPlaying = false;
        if (icon) icon.className = "fa-solid fa-cloud-rain";
        if (text) text.innerText = (currentLang === 'si') ? "සන්සුන් වැස්ස" : "Calm Rain";
        if (btn) btn.classList.remove('playing');
    }
}

function startAmbientNoise() {
    try {
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        if (!audioCtx) audioCtx = new AudioContext();
        if (audioCtx.state === 'suspended') audioCtx.resume();

        // Generate synthetic gentle pink noise rain stream
        const bufferSize = audioCtx.sampleRate * 2;
        const noiseBuffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
        const output = noiseBuffer.getChannelData(0);
        let b0 = 0, b1 = 0, b2 = 0;
        for (let i = 0; i < bufferSize; i++) {
            const white = Math.random() * 2 - 1;
            b0 = 0.99886 * b0 + white * 0.0555179;
            b1 = 0.99332 * b1 + white * 0.0750759;
            b2 = 0.96900 * b2 + white * 0.1538520;
            output[i] = (b0 + b1 + b2) * 0.11;
        }

        noiseNode = audioCtx.createBufferSource();
        noiseNode.buffer = noiseBuffer;
        noiseNode.loop = true;

        gainNode = audioCtx.createGain();
        gainNode.gain.setValueAtTime(0.08, audioCtx.currentTime); // Soft background volume

        noiseNode.connect(gainNode);
        gainNode.connect(audioCtx.destination);
        noiseNode.start();
    } catch (e) {
        console.warn("Web Audio API not supported", e);
    }
}

function stopAmbientNoise() {
    try {
        if (noiseNode) {
            noiseNode.stop();
            noiseNode.disconnect();
            noiseNode = null;
        }
    } catch (e) {
        console.warn(e);
    }
}

/* ==========================================================================
   11. AI WELLNESS CHATBOT
   ========================================================================== */
function toggleChatWindow() {
    const drawer = document.getElementById('chatDrawer');
    if (!drawer) return;
    drawer.style.display = (drawer.style.display === 'flex') ? 'none' : 'flex';
}

function sendQuickPrompt(promptText) {
    const input = document.getElementById('chatInputField');
    if (input) {
        input.value = promptText;
        sendChatMessage();
    }
}

function sendChatMessage() {
    const input = document.getElementById('chatInputField');
    const text = input?.value?.trim();
    if (!text) return;

    input.value = '';
    appendChatBubble(text, 'user');

    // Generate response
    setTimeout(() => {
        const reply = generateAiReply(text);
        appendChatBubble(reply, 'bot');
    }, 600);
}

function appendChatBubble(msg, sender) {
    const stream = document.getElementById('chatStream');
    if (!stream) return;

    const div = document.createElement('div');
    div.className = `chat-bubble ${sender}`;
    div.innerText = msg;
    stream.appendChild(div);
    stream.scrollTop = stream.scrollHeight;
}

function generateAiReply(rawQuery) {
    const q = rawQuery.toLowerCase();
    const isSi = /[\u0D80-\u0DFF]/.test(rawQuery) || currentLang === 'si';

    if (q.includes('exam') || q.includes('study') || q.includes('විභාග') || q.includes('පාඩම්')) {
        return isSi
            ? "විභාග කාලයේදී ඇතිවන පීඩනය සාමාන්‍ය දෙයකි. එක්වරම සියල්ල කිරීමට උත්සාහ නොකර Pomodoro ක්‍රමය (විනාඩි 25ක් පාඩම් කර විනාඩි 5ක විවේකයක්) අනුගමනය කරන්න. ඔබගේ මනස සන්සුන් කරගැනීමට මෙහි ඇති 4-7-8 හුස්ම ගැනීමේ ව්‍යායාමය උත්සාහ කරන්න."
            : "Academic and exam stress can feel overwhelming. Break your study load into 25-minute Pomodoro sessions with 5-minute pauses. Ensure you take short walks and drink enough water. Try our 4-7-8 Breathing tool to regain focus!";
    }

    if (q.includes('panic') || q.includes('anxiety') || q.includes('බය') || q.includes('කලබල')) {
        return isSi
            ? "අධික නොසන්සුන් බවක් හෝ panic එකක් දැනේ නම්, වහාම අපගේ 5-4-3-2-1 Grounding ක්‍රමය භාවිතා කර අවට ඇති වස්තූන් 5ක් දෙස බලන්න. ගැඹුරින් හුස්ම 3ක් ඉහළට ගෙන සෙමින් පිටකරන්න."
            : "If you feel sudden panic or acute anxiety, ground yourself immediately using the 5-4-3-2-1 technique: notice 5 things you can see, 4 you can feel, 3 you can hear. Inhale deeply for 4 seconds and exhale slowly.";
    }

    if (q.includes('1926') || q.includes('helpline') || q.includes('දුරකථන') || q.includes('හදිසි')) {
        return isSi
            ? "ජාතික මානසික සෞඛ්‍ය විද්‍යායතනයේ 1926 ක්ෂණික ඇමතුම් අංකය පැය 24 පුරාම නොමිලේ සහ උපරිම රහස්‍යභාවයෙන් යුතුව ක්‍රියාත්මක වේ. අවශ්‍ය ඕනෑම අවස්ථාවක ඔවුන් අමතන්න."
            : "The 1926 National Mental Health Helpline is completely free, 24/7, and 100% confidential. You can dial 1926 directly from any phone in Sri Lanka for immediate counseling.";
    }

    if (q.includes('counsel') || q.includes('appointment') || q.includes('උපදේශන')) {
        return isSi
            ? "විශ්වවිද්‍යාල උපදේශන සේවාව සමඟ සම්බන්ධ වීමට අපගේ 'Schedule Counseling' බොත්තම ඔබන්න. අවශ්‍ය නම් ඔබට ඔබේ අනන්‍යතාවය සඟවා (Anonymous) ඉල්ලුම් කළ හැක."
            : "You can book a confidential session with the campus counseling team right now using the 'Schedule Counseling' button. Standard and Anonymous session options are supported.";
    }

    if (q.includes('sad') || q.includes('depress') || q.includes('දුක') || q.includes('තනිකම')) {
        return isSi
            ? "ඔබට තනිකමක් හෝ දුකක් දැනෙන බව ඇසීම ගැන කණගාටුයි. ඔබ තනිවී නැත. මෙවැනි හැඟීම් විශ්වාසදායක කෙනෙකු හෝ වෘත්තීය උපදේශකයෙකු සමඟ බෙදාගැනීම විශාල සහනයක් වනු ඇත. අද දින ඔබේ මනෝභාවය සටහන් කර PHQ-9 පරීක්ෂාව සිදු කරන්න."
            : "It takes courage to acknowledge sadness or emotional weight. Please remember that you are never alone. Taking small steps, getting enough rest, and speaking to our counselor can make an enormous difference.";
    }

    return isSi
        ? "ඔබගේ පණිවිඩයට ස්තූතියි. ඔබේ මානසික සුවතාවය ඉහළ නංවා ගැනීමට PHQ-9 හෝ GAD-7 පරීක්ෂාවන් සිදු කිරීමට, හුස්ම ගැනීමේ ව්‍යායාම කිරීමට හෝ උපදේශකවරයෙකු හමුවීමට මෙම පද්ධතිය ඔබට සහාය වේ."
        : "Thank you for reaching out. I'm here to support your mental wellness journey. You can take a PHQ-9 or GAD-7 self-check, practice breathing exercises, or connect with our campus counselors anytime.";
}

/* ==========================================================================
   12. CLINICS DIRECTORY TABLE
   ========================================================================== */
function renderClinicsTable() {
    const tbody = document.getElementById('clinicsTableBody');
    if (!tbody) return;

    let html = '';
    CLINIC_DIRECTORY.forEach(item => {
        html += `
            <tr>
                <td><strong>${item.district}</strong></td>
                <td>${item.hospital}</td>
                <td>
                    <a href="tel:${item.phone.replace(/\s+/g, '')}" style="color:var(--primary); text-decoration:none; font-weight:700;">
                        <i class="fa-solid fa-phone"></i> ${item.phone}
                    </a>
                </td>
            </tr>
        `;
    });
    tbody.innerHTML = html;
}

/* ==========================================================================
   13. HISTORICAL TRAJECTORY CHART (CHART.JS)
   ========================================================================== */
async function initTrendChart() {
    const canvas = document.getElementById('mainTrendsChart');
    if (!canvas) return;

    try {
        const res = await fetch(SITE_ROOT + '/api/assessment.php');
        const data = await res.json();
        const history = data.history || [];

        const labels = [];
        const phqPoints = [];
        const gadPoints = [];

        history.slice(-8).forEach(h => {
            labels.push(h.created_at ? h.created_at.substring(5, 10) : 'Test');
            if (h.test_type === 'phq9') {
                phqPoints.push(h.score);
                gadPoints.push(null);
            } else {
                phqPoints.push(null);
                gadPoints.push(h.score);
            }
        });

        if (trendChartInstance) {
            trendChartInstance.destroy();
        }

        const ctx = canvas.getContext('2d');
        trendChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['Entry 1', 'Entry 2', 'Entry 3'],
                datasets: [
                    {
                        label: 'PHQ-9 (Depression)',
                        data: phqPoints.length ? phqPoints : [4, 6, 3],
                        borderColor: '#1e4d2b',
                        backgroundColor: 'rgba(30, 77, 43, 0.1)',
                        tension: 0.35,
                        spanGaps: true,
                        fill: true
                    },
                    {
                        label: 'GAD-7 (Anxiety)',
                        data: gadPoints.length ? gadPoints : [5, 7, 4],
                        borderColor: '#0284c7',
                        backgroundColor: 'rgba(2, 132, 199, 0.1)',
                        tension: 0.35,
                        spanGaps: true,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, max: 27 }
                }
            }
        });
    } catch (e) {
        console.warn("Could not load trend chart", e);
    }
}
