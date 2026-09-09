/**
 * Student Mental Wellness Check-in System (Sansun)
 * Master Client-Side Logic (Bilingual, Assessments, Chatbot, Modals, Audio)
 */

/* 1. Translations Dictionary */
const TRANSLATIONS = {
    si: {
        brandName: "සන්සුන්", navBreath: "හුස්ම", navAssessment: "ඇගයීම", navMood: "Mood Journal", navCounseling: "උපදේශනය", navHistory: "ප්‍රගතිය", navDirectory: "සායන", navLogin: "ඇතුළු වන්න",
        heroTitle: "ඔබේ මානසික සුවතාවය වෙනුවෙන් සුරක්ෂිත ඉඩක්", heroDesc: "විභාග සහ අධ්‍යාපනික පීඩනය හඳුනාගෙන, මනස සන්සුන් කරගැනීමට අවශ්‍ය වෘත්තීය මගපෙන්වීම් සහ උපදේශන පහසුකම් මෙහි ඇතුළත් වේ.", heroBtn: "පරීක්ෂාව ආරම්භ කරන්න",
        moodTitle: "දෛනික මනෝභාවය සටහන් කරන්න (Daily Mood Journal)", moodDesc: "අද දිනයේ ඔබට දැනෙන හැඟීම තෝරන්න:", btnSaveMood: "මනෝභාවය Save කරන්න", moodPlaceholder: "අද දිනය ගැන කුඩා සටහනක් තබන්න (Optional)...", recentEntries: "මෑත සටහන්:",
        bannerTitle: "ඔබට කවුරුන් හෝ සමඟ කතා කිරීමට අවශ්‍යද?", bannerDesc: "විශ්වවිද්‍යාල උපදේශකවරයෙකු හා සම්බන්ධ වීමට හෝ ක්ෂණික සහාය ලබා ගැනීමට ඉදිරියට යන්න.",
        btnBookCounselor: "උපදේශන වාරයක් වෙන්කරගන්න", btnCall1926: "1926 අමතන්න",
        modalLoginHeader: "ගිණුමට පිවිසෙන්න (Login)", lblEmail: "විද්‍යුත් තැපෑල හෝ ශිෂ්‍ය අංකය", lblPassword: "මුරපදය (Password)", btnLoginSubmit: "ඇතුළු වන්න",
        modalCounselHeader: "උපදේශන සේවාව හා සම්බන්ධ වන්න", lblPrivacy: "රහස්‍යතාවය (Privacy Option)", optNamed: "සාමාන්‍ය (නම සහ ශිෂ්‍ය අංකය ඇතුළත් කරන්න)", optAnon: "අඥාත අයුරින් (Anonymous Request)",
        lblName: "සම්පූර්ණ නම / ශිෂ්‍ය අංකය", lblMode: "උපදේශන ක්‍රමය", optOnline: "මාර්ගගත (Online Chat / Video Call)", optInPerson: "සෘජුව (In-Person Office Session)",
        lblDate: "කැමති දිනය සහ වේලාව", lblNotes: "කෙටි සටහනක් (Optional)", btnSubmitCounsel: "ඉල්ලීම යොමු කරන්න",
        tabPhq: "PHQ-9 (විෂාදය / Depression)", tabGad: "GAD-7 (කාංසාව / Anxiety)", btnPrev: "ආපසු", btnNext: "ඉදිරියට", btnReTest: "නැවත පරීක්ෂා කරන්න", scoreTitle: "ලකුණු මට්ටම", emergencyWarn: "⚠️ ක්ෂණික සහාය ලබා ගැනීමට උපදෙස් දෙනු ලැබේ.", completeMsg: "පරීක්ෂාව සම්පූර්ණයි.",
        chartTitle: "ඔබේ ප්‍රගතිය (Past Scores)", chartLabel: "ලකුණු සටහන", chartLocked: "🔒 PIN එක මගින් ආරක්ෂිතයි. බලන්න Unlock කරන්න.", pinLockBtn: "PIN Lock", pinUnlockBtn: "Unlocked",
        dirTitle: "දිස්ත්‍රික්ක අනුව මානසික සෞඛ්‍ය සායන (Resource Directory)", thDistrict: "දිස්ත්‍රික්කය", thHospital: "රෝහල / මධ්‍යස්ථානය", thContact: "දුරකථන අංකය",
        checkTitle: "දෛනික මනෝවිද්‍යාත්මක පුරුදු (Daily Self-Care)", chk1: "විනාඩි 10ක් හුස්ම ගැනීමේ ව්‍යායාම කිරීම", chk2: "වතුර ලීටර 2ක් ලබාගැනීම", chk3: "විනාඩි 15ක් එළිමහනේ ඇවිදීම", chk4: "පැය 7-8ක සුවබර නින්දක් ලැබීම",
        helpTitle: "ඔබට හදිසි සහායක් අවශ්‍යද?", helpDesc: "ඔබ දැඩි මානසික පීඩනයකින් පසුවන්නේ නම්, නොමිලේ සහ උපරිම රහස්‍යභාවයෙන් යුතුව සහාය ලබාගැනීමට පහත සේවාවන් අමතන්න.",
        help1: "ජාතික මානසික සෞඛ්‍ය විද්‍යායතනය", help2: "CCC Line (24/7 නොමිලේ)", help3: "ශ්‍රී ලංකා සුමිත්‍රයෝ",
        breathModalSub: "මනස සන්සුන් කර ගැනීමට පහත උපදෙස් අනුගමනය කරන්න.", closeBreathBtn: "වසා දමන්න", audioLabel: "සොබාදහමේ ශබ්ද (Ambient Sound):", btnWa: "WhatsApp Support",
        breathTextReady: "ලෑස්ති වන්න...", breathTextInhale: "හුස්ම ගන්න", breathInstInhale: "තත්පර 4ක් තදින් හුස්ම ගන්න...", breathTextHold: "රඳවා ගන්න", breathInstHold: "තත්පර 7ක් හුස්ම තදකර තබාගන්න...", breathTextExhale: "පිටකරන්න", breathInstExhale: "තත්පර 8ක් පුරා හෙමින් හුස්ම පිටකරන්න...",
        groundTitle: "5-4-3-2-1 Grounding Technique", groundSub: "Panic Attack එකක් හෝ අධික බියක් දැනෙන විට මනස වර්තමානයට ගෙන ඒමට මෙය භාවිතා කරන්න:",
        groundList: [
            "👀 <strong>5 - ඔබ අවට පෙනෙන දේවල් 5ක්</strong> දෙස අවධානයෙන් බලන්න.",
            "✋ <strong>4 - ඔබට ඇල්ලිය හැකි දේවල් 4ක්</strong> අතගා බලන්න.",
            "👂 <strong>3 - ඔබට ඇසෙන ශබ්ද 3කට</strong> සවන් දෙන්න.",
            "👃 <strong>2 - ඔබට දැනෙන සුවඳ වර්ග 2ක්</strong> කෙරෙහි අවධානය යොමු කරන්න.",
            "👅 <strong>1 - ඔබට දැනෙන රහක් 1ක්</strong> ගැන සිතන්න."
        ],
        groundClose: "තේරුණා / Close",
        chatTitle: "සන්සුන් AI සහායක", chatPlaceholder: "ඔබේ පණිවිඩය ටයිප් කරන්න...",
        botIntro: "ආයුබෝවන්! 👋 මම 'සන්සුන්' AI සහායක. ඔබට අද දැනෙන දේ හෝ සිතට වදදෙන ඕනෑම දෙයක් මා සමඟ බෙදාගන්න පුළුවන්. මා ඔබට උදවු කරන්නේ කෙසේද?",
        directoryData: [
            { district: "කොළඹ", hospital: "ජාතික මානසික සෞඛ්‍ය විද්‍යායතනය (NIMH), අංගොඩ", phone: "011 2578234" },
            { district: "මහනුවර", hospital: "ශික්ෂණ රෝහල, පේරාදෙණිය (Mental Health Unit)", phone: "081 2388000" },
            { district: "ගාල්ල", hospital: "කරාපිටිය ශික්ෂණ රෝහල", phone: "091 2232250" },
            { district: "යාපනය", hospital: "ශික්ෂණ රෝහල, යාපනය", phone: "021 2222261" },
            { district: "කුරුණෑගල", hospital: "ශික්ෂණ රෝහල, කුරුණෑගල", phone: "037 2222261" }
        ],
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
        ],
        options: ["කොහෙත්ම නැත (0)", "දින කිහිපයක් (1)", "සතියකට වැඩි දින ගණනක් (2)", "දිනපතාම වාගේ (3)"]
    },
    en: {
        brandName: "Sansun", navBreath: "Breathing", navAssessment: "Assessment", navMood: "Mood Journal", navCounseling: "Counseling", navHistory: "History", navDirectory: "Directory", navLogin: "Login",
        heroTitle: "A Safe Space for Your Mental Well-being", heroDesc: "Identify academic and exam pressure early and access institutional counseling and relaxation tools effectively.", heroBtn: "Start Assessment",
        moodTitle: "Daily Mood Journal", moodDesc: "Select how you are feeling today:", btnSaveMood: "Save Mood Entry", moodPlaceholder: "Write a short note about today (Optional)...", recentEntries: "Recent Entries:",
        bannerTitle: "Need someone to talk to?", bannerDesc: "Connect with a qualified student counselor or access confidential support services anytime.",
        btnBookCounselor: "Book Counseling Session", btnCall1926: "Call 1926",
        modalLoginHeader: "Login to Your Account", lblEmail: "Email or Student ID", lblPassword: "Password", btnLoginSubmit: "Login",
        modalCounselHeader: "Connect with a Counselor", lblPrivacy: "Privacy Option", optNamed: "Standard (Include Name & Student ID)", optAnon: "Anonymous Request",
        lblName: "Full Name / Student ID", lblMode: "Preferred Session Mode", optOnline: "Online Chat / Video Call", optInPerson: "In-Person (Counseling Office)",
        lblDate: "Preferred Date & Time", lblNotes: "Brief Note (Optional)", btnSubmitCounsel: "Submit Request",
        tabPhq: "PHQ-9 (Depression)", tabGad: "GAD-7 (Anxiety)", btnPrev: "Back", btnNext: "Next", btnReTest: "Retake Assessment", scoreTitle: "Score Result", emergencyWarn: "⚠️ Immediate counseling / medical support recommended.", completeMsg: "Assessment complete.",
        chartTitle: "Your Progress (Past Scores)", chartLabel: "Score History", chartLocked: "🔒 Protected by PIN. Please unlock to view.", pinLockBtn: "PIN Lock", pinUnlockBtn: "Unlocked",
        dirTitle: "District Mental Health Resource Directory", thDistrict: "District", thHospital: "Hospital / Clinic", thContact: "Contact Number",
        checkTitle: "Daily Self-Care Checklist", chk1: "10 minutes breathing exercise", chk2: "Drink 2 liters of water", chk3: "15 minutes outdoor walk", chk4: "7-8 hours sound sleep",
        helpTitle: "Need Immediate Support?", helpDesc: "If you are experiencing severe distress, reach out to these free helplines anytime with complete confidentiality.",
        help1: "National Institute of Mental Health", help2: "CCC Line (24/7 Free)", help3: "Sri Lanka Sumithrayo",
        breathModalSub: "Follow the instructions below to relax your mind.", closeBreathBtn: "Close", audioLabel: "Nature Ambient Sound:", btnWa: "WhatsApp Support",
        breathTextReady: "Get Ready...", breathTextInhale: "Inhale", breathInstInhale: "Inhale deeply for 4 seconds...", breathTextHold: "Hold", breathInstHold: "Hold your breath for 7 seconds...", breathTextExhale: "Exhale", breathInstExhale: "Exhale slowly for 8 seconds...",
        groundTitle: "5-4-3-2-1 Grounding Technique", groundSub: "Use this to ground your mind in the present moment during high anxiety or panic attacks:",
        groundList: [
            "👀 <strong>5 - Look at 5 things</strong> around you carefully.",
            "✋ <strong>4 - Touch 4 physical things</strong> you can feel.",
            "👂 <strong>3 - Listen to 3 distinct sounds</strong> around you.",
            "👃 <strong>2 - Notice 2 scents or smells</strong> in your environment.",
            "👅 <strong>1 - Focus on 1 taste</strong> in your mouth."
        ],
        groundClose: "Got it / Close",
        chatTitle: "Sansun AI Assistant", chatPlaceholder: "Type your message...",
        botIntro: "Hello! 👋 I am the 'Sansun' AI Assistant. Feel free to share how you are feeling. How can I help you today?",
        directoryData: [
            { district: "Colombo", hospital: "National Institute of Mental Health (NIMH), Angoda", phone: "011 2578234" },
            { district: "Kandy", hospital: "Teaching Hospital, Peradeniya (Mental Health Unit)", phone: "081 2388000" },
            { district: "Galle", hospital: "Karapitiya Teaching Hospital", phone: "091 2232250" },
            { district: "Jaffna", hospital: "Teaching Hospital, Jaffna", phone: "021 2222261" },
            { district: "Kurunegala", hospital: "Teaching Hospital, Kurunegala", phone: "037 2222261" }
        ],
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
        ],
        options: ["Not at all (0)", "Several days (1)", "More than half the days (2)", "Nearly every day (3)"]
    }
};

/* 2. Global State */
let currentLang = 'si';
let currentTest = 'phq9';
let currentStep = 0;
let answers = {};
let isTestCompleted = false;
let selectedMoodVal = '';
let isUnlocked = true;
let chartInstance = null;
let breathTimeoutIds = [];
let currentBreathPhase = 'ready';

document.addEventListener('DOMContentLoaded', () => {
    // Theme preference
    const savedTheme = localStorage.getItem('sansun_theme');
    if (savedTheme === 'dark') {
        document.body.setAttribute('data-theme', 'dark');
    }

    renderCurrentStep();
    renderGroundingList();
    renderDirectoryTable();
    loadMoodLogs();
    loadAssessmentChart();
});

/* 3. Theme & Language Controls */
function toggleTheme() {
    const isDark = document.body.getAttribute('data-theme') === 'dark';
    document.body.setAttribute('data-theme', isDark ? '' : 'dark');
    localStorage.setItem('sansun_theme', isDark ? 'light' : 'dark');
    if (chartInstance) chartInstance.destroy();
    loadAssessmentChart();
}

function toggleLanguage() {
    currentLang = (currentLang === 'si') ? 'en' : 'si';
    document.getElementById('langTxt').innerText = (currentLang === 'si') ? 'English' : 'සිංහල';
    const t = TRANSLATIONS[currentLang];

    document.getElementById('brandName').innerText = t.brandName;
    document.getElementById('navBreath').innerText = t.navBreath;
    document.getElementById('navAssessment').innerText = t.navAssessment;
    document.getElementById('navMood').innerText = t.navMood;
    document.getElementById('navCounseling').innerText = t.navCounseling;
    document.getElementById('navHistory').innerText = t.navHistory;
    document.getElementById('navDirectory').innerText = t.navDirectory;

    const navLoginEl = document.getElementById('navLogin');
    if (navLoginEl) navLoginEl.innerText = t.navLogin;

    document.getElementById('heroTitle').innerText = t.heroTitle;
    document.getElementById('heroDesc').innerText = t.heroDesc;
    document.getElementById('heroBtn').innerText = t.heroBtn;

    document.getElementById('moodTitle').innerText = t.moodTitle;
    document.getElementById('moodDesc').innerText = t.moodDesc;
    document.getElementById('btnSaveMood').innerText = t.btnSaveMood;
    document.getElementById('moodNote').placeholder = t.moodPlaceholder;

    document.getElementById('bannerTitle').innerText = t.bannerTitle;
    document.getElementById('bannerDesc').innerText = t.bannerDesc;
    document.getElementById('btnBookCounselor').innerText = t.btnBookCounselor;
    document.getElementById('btnCall1926').innerText = t.btnCall1926;

    document.getElementById('modalCounselHeader').innerText = t.modalCounselHeader;
    document.getElementById('lblPrivacy').innerText = t.lblPrivacy;
    document.getElementById('optNamed').innerText = t.optNamed;
    document.getElementById('optAnon').innerText = t.optAnon;
    document.getElementById('lblName').innerText = t.lblName;
    document.getElementById('lblMode').innerText = t.lblMode;
    document.getElementById('optOnline').innerText = t.optOnline;
    document.getElementById('optInPerson').innerText = t.optInPerson;
    document.getElementById('lblDate').innerText = t.lblDate;
    document.getElementById('lblNotes').innerText = t.lblNotes;
    document.getElementById('btnSubmitCounsel').innerText = t.btnSubmitCounsel;

    document.getElementById('tabPhq').innerText = t.tabPhq;
    document.getElementById('tabGad').innerText = t.tabGad;
    document.getElementById('prevBtn').innerText = t.btnPrev;
    document.getElementById('nextBtn').innerText = t.btnNext;

    document.getElementById('chartTitle').innerText = t.chartTitle;
    document.getElementById('dirTitle').innerText = t.dirTitle;
    document.getElementById('thDistrict').innerText = t.thDistrict;
    document.getElementById('thHospital').innerText = t.thHospital;
    document.getElementById('thContact').innerText = t.thContact;

    document.getElementById('checkTitle').innerText = t.checkTitle;
    document.getElementById('chk1').innerText = t.chk1;
    document.getElementById('chk2').innerText = t.chk2;
    document.getElementById('chk3').innerText = t.chk3;
    document.getElementById('chk4').innerText = t.chk4;

    document.getElementById('helpTitle').innerText = t.helpTitle;
    document.getElementById('helpDesc').innerText = t.helpDesc;
    document.getElementById('help1').innerText = t.help1;
    document.getElementById('help2').innerText = t.help2;
    document.getElementById('help3').innerText = t.help3;

    document.getElementById('groundTitle').innerText = t.groundTitle;
    document.getElementById('groundSub').innerText = t.groundSub;
    document.getElementById('groundCloseBtn').innerText = t.groundClose;

    document.getElementById('breathModalSub').innerText = t.breathModalSub;
    document.getElementById('closeBreathBtn').innerText = t.closeBreathBtn;
    document.getElementById('audioLabel').innerHTML = `<i class="fa-solid fa-music"></i> ${t.audioLabel}`;
    document.getElementById('btnWa').innerText = t.btnWa;

    document.getElementById('chatTitle').innerText = t.chatTitle;
    document.getElementById('chatInput').placeholder = t.chatPlaceholder;
    document.getElementById('botIntroMsg').innerText = t.botIntro;

    renderGroundingList();
    renderDirectoryTable();
    if (!isTestCompleted) {
        renderCurrentStep();
    }
}

/* 4. Ambient Audio */
function toggleAudio() {
    const audio = document.getElementById('ambientAudio');
    const icon = document.getElementById('audioIcon');
    if (audio.paused) { 
        audio.play(); 
        icon.className = "fa-solid fa-pause"; 
    } else { 
        audio.pause(); 
        icon.className = "fa-solid fa-play"; 
    }
}

/* 5. Mood Journal */
function selectMood(emoji, el) {
    selectedMoodVal = emoji;
    document.querySelectorAll('.mood-btn').forEach(b => b.classList.remove('selected'));
    if (el) el.classList.add('selected');
}

async function saveMoodEntry() {
    if (!selectedMoodVal) {
        alert(currentLang === 'si' ? "කරුණාකර මනෝභාවයක් තෝරන්න." : "Please select a mood first.");
        return;
    }
    const note = document.getElementById('moodNote').value;
    try {
        const res = await fetch(SITE_ROOT + '/api/mood.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ mood: selectedMoodVal, note: note })
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('moodNote').value = '';
            selectedMoodVal = '';
            document.querySelectorAll('.mood-btn').forEach(b => b.classList.remove('selected'));
            alert(currentLang === 'si' ? "මනෝභාවය සාර්ථකව සටහන් විය!" : "Mood entry saved successfully!");
            loadMoodLogs();
        }
    } catch (e) {
        alert("Could not connect to database: " + e.message);
    }
}

async function loadMoodLogs() {
    try {
        const res = await fetch(SITE_ROOT + '/api/mood.php');
        const data = await res.json();
        const container = document.getElementById('moodLogList');
        if (data.logs && data.logs.length > 0) {
            let html = `<strong>${TRANSLATIONS[currentLang].recentEntries}</strong><ul style="list-style:none; margin-top:8px;">`;
            data.logs.slice(0, 5).forEach(item => {
                html += `<li style="padding:6px 0; border-bottom:1px solid var(--border); display:flex; justify-content:space-between;">
                    <span>${item.mood_emoji} <strong>${item.mood_label}</strong>: ${item.note || '-'}</span>
                    <small style="color:var(--text-muted);">${item.created_at.substring(0, 16)}</small>
                </li>`;
            });
            html += '</ul>';
            container.innerHTML = html;
        }
    } catch (e) {
        console.error("Mood fetch error", e);
    }
}

/* 6. Assessment Wizard (PHQ-9 & GAD-7) */
function switchTest(type) {
    currentTest = type;
    currentStep = 0;
    answers = {};
    isTestCompleted = false;

    document.getElementById('tabPhq').classList.toggle('active', type === 'phq9');
    document.getElementById('tabGad').classList.toggle('active', type === 'gad7');
    document.getElementById('result').style.display = 'none';
    document.getElementById('wizardForm').style.display = 'block';

    renderCurrentStep();
}

function renderCurrentStep() {
    const t = TRANSLATIONS[currentLang];
    const questions = t[currentTest];
    const total = questions.length;
    const progress = ((currentStep) / total) * 100;
    document.getElementById('progressFill').style.width = `${progress}%`;

    const container = document.getElementById('questionsContainer');
    const qText = questions[currentStep];

    let optionsHtml = '';
    t.options.forEach((opt, idx) => {
        const checked = answers[currentStep] === idx ? 'checked' : '';
        optionsHtml += `
            <label>
                <input type="radio" name="ans" value="${idx}" ${checked} onchange="selectAnswer(${idx})">
                <span class="option-btn">${opt}</span>
            </label>
        `;
    });

    container.innerHTML = `
        <div style="font-size:0.85rem; color:var(--text-muted); margin-bottom:8px;">
            Question ${currentStep + 1} of ${total}
        </div>
        <h3 style="font-size:1.15rem; margin-bottom:16px; color:var(--text-main);">${qText}</h3>
        <div class="options-group">${optionsHtml}</div>
    `;

    document.getElementById('prevBtn').style.display = currentStep > 0 ? 'inline-block' : 'none';
    document.getElementById('nextBtn').innerText = currentStep === total - 1 ? (currentLang === 'si' ? 'අවසන් කරන්න' : 'Finish') : t.btnNext;
}

function selectAnswer(val) {
    answers[currentStep] = val;
}

function navigateStep(direction) {
    const questions = TRANSLATIONS[currentLang][currentTest];
    if (direction === 1 && answers[currentStep] === undefined) {
        alert(currentLang === 'si' ? "කරුණාකර පිළිතුරක් තෝරන්න." : "Please select an answer.");
        return;
    }

    currentStep += direction;

    if (currentStep >= questions.length) {
        finishAssessment();
    } else {
        renderCurrentStep();
    }
}

async function finishAssessment() {
    isTestCompleted = true;
    document.getElementById('progressFill').style.width = '100%';
    document.getElementById('wizardForm').style.display = 'none';

    // Calculate score
    let totalScore = 0;
    Object.values(answers).forEach(v => totalScore += Number(v));

    // Submit to API
    try {
        const res = await fetch(SITE_ROOT + '/api/assessment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                test_type: currentTest,
                score: totalScore,
                answers: answers
            })
        });
        const data = await res.json();
        renderResultScreen(data);
        loadAssessmentChart();
    } catch (e) {
        alert("Could not record assessment: " + e.message);
    }
}

function renderResultScreen(resData) {
    const resBox = document.getElementById('result');
    resBox.style.display = 'block';

    const highRiskBanner = resData.is_high_risk ? `
        <div style="background:#fee2e2; border-left:4px solid #ef4444; color:#b91c1c; padding:16px; border-radius:10px; margin:20px 0; text-align:left;">
            <strong>⚠️ අවධානය යොමු කරන්න:</strong>
            <p style="font-size:0.9rem; margin-top:4px;">ඔබගේ ලකුණු මට්ටම අනුව ක්ෂණික වෘත්තීය උපදේශනයක් හෝ සහන සේවාවක් ලබා ගැනීම දැඩි ලෙස නිර්දේශ කෙරේ. කරුණාකර පහත <strong>1926</strong> නොමිලේ අමතන්න හෝ විශ්වවිද්‍යාල උපදේශකවරයා හා සම්බන්ධ වන්න.</p>
        </div>
    ` : '';

    resBox.innerHTML = `
        <div style="width:70px; height:70px; border-radius:50%; background:${resData.is_high_risk ? '#fee2e2' : '#dcfce7'}; color:${resData.is_high_risk ? '#ef4444' : '#15803d'}; display:flex; align-items:center; justify-content:center; font-size:2rem; margin:0 auto 16px auto;">
            <i class="fa-solid ${resData.is_high_risk ? 'fa-triangle-exclamation' : 'fa-circle-check'}"></i>
        </div>
        <h3 style="font-size:1.4rem; color:var(--text-main); margin-bottom:6px;">පරීක්ෂාව සම්පූර්ණයි</h3>
        <p style="color:var(--text-muted); font-size:0.9rem;">${currentTest.toUpperCase()} Assessment Result</p>

        <div style="background:var(--bg); padding:20px; border-radius:14px; margin:20px 0; border:1px solid var(--border);">
            <div style="font-size:2.4rem; font-weight:700; color:var(--primary);">${resData.score}</div>
            <div style="font-weight:700; font-size:1.1rem; color:var(--text-main); margin-top:4px;">${resData.severity}</div>
            <p style="font-size:0.9rem; color:var(--text-muted); margin-top:8px;">${resData.recommendation}</p>
        </div>

        ${highRiskBanner}

        <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap; margin-top:20px;">
            <button class="btn-action" onclick="switchTest('${currentTest}')">
                <i class="fa-solid fa-rotate-right"></i> නැවත පරීක්ෂා කරන්න
            </button>
            <button class="btn-action" style="background:var(--accent);" onclick="openCounselingModal()">
                <i class="fa-solid fa-user-doctor"></i> උපදේශනයක් වෙන්කරගන්න
            </button>
        </div>
    `;
}

/* 7. Progress History Chart */
async function loadAssessmentChart() {
    try {
        const res = await fetch(SITE_ROOT + '/api/assessment.php');
        const data = await res.json();
        const ctx = document.getElementById('historyChart');
        if (!ctx) return;

        if (chartInstance) chartInstance.destroy();

        const labels = data.history.map(item => item.created_at.substring(5, 10));
        const phqScores = data.history.filter(i => i.test_type === 'phq9').map(i => i.total_score);
        const gadScores = data.history.filter(i => i.test_type === 'gad7').map(i => i.total_score);

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels.slice(-8),
                datasets: [
                    {
                        label: 'PHQ-9 (Depression / විෂාදය)',
                        data: phqScores.slice(-8),
                        borderColor: '#4a7c59',
                        backgroundColor: 'rgba(74, 124, 89, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'GAD-7 (Anxiety / කාංසාව)',
                        data: gadScores.slice(-8),
                        borderColor: '#5b82a6',
                        backgroundColor: 'rgba(91, 130, 166, 0.1)',
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, max: 27 }
                }
            }
        });
    } catch (e) {
        console.error("Chart load error", e);
    }
}

function togglePinProtection() {
    const el = document.getElementById('protectedHistoryContent');
    const btn = document.getElementById('btnPinToggle');
    if (isUnlocked) {
        el.style.filter = 'blur(10px)';
        btn.innerHTML = `<i class="fa-solid fa-lock"></i> Locked`;
        isUnlocked = false;
    } else {
        const pin = prompt("PIN අංකය ඇතුළත් කරන්න (Demo PIN: 1234):");
        if (pin === '1234' || pin === '0000') {
            el.style.filter = 'none';
            btn.innerHTML = `<i class="fa-solid fa-lock-open"></i> Unlocked`;
            isUnlocked = true;
        } else {
            alert("වැරදි PIN අංකයකි!");
        }
    }
}

/* 8. Resource Directory & Grounding */
function renderDirectoryTable() {
    const tbody = document.getElementById('directoryBody');
    const data = TRANSLATIONS[currentLang].directoryData;
    let html = '';
    data.forEach(item => {
        html += `
            <tr>
                <td><strong>${item.district}</strong></td>
                <td>${item.hospital}</td>
                <td><a href="tel:${item.phone.replace(/\\s/g,'')}" style="color:var(--primary); font-weight:700;"><i class="fa-solid fa-phone"></i> ${item.phone}</a></td>
            </tr>
        `;
    });
    tbody.innerHTML = html;
}

function renderGroundingList() {
    const ul = document.getElementById('groundList');
    const items = TRANSLATIONS[currentLang].groundList;
    ul.innerHTML = items.map(li => `<li>${li}</li>`).join('');
}

/* 9. Breathing Modal (4-7-8) */
function openBreathingModal() {
    document.getElementById('breathModal').style.display = 'flex';
    startBreathingCycle();
}

function closeBreathingModal() {
    document.getElementById('breathModal').style.display = 'none';
    breathTimeoutIds.forEach(id => clearTimeout(id));
    breathTimeoutIds = [];
}

function startBreathingCycle() {
    const circle = document.getElementById('breathCircle');
    const inst = document.getElementById('breathInstruction');
    const t = TRANSLATIONS[currentLang];

    circle.className = 'breath-circle expand';
    circle.innerText = t.breathTextInhale;
    inst.innerText = t.breathInstInhale;

    breathTimeoutIds.push(setTimeout(() => {
        circle.className = 'breath-circle hold';
        circle.innerText = t.breathTextHold;
        inst.innerText = t.breathInstHold;

        breathTimeoutIds.push(setTimeout(() => {
            circle.className = 'breath-circle shrink';
            circle.innerText = t.breathTextExhale;
            inst.innerText = t.breathInstExhale;

            breathTimeoutIds.push(setTimeout(() => {
                startBreathingCycle();
            }, 8000));
        }, 7000));
    }, 4000));
}

function openGroundingModal() { document.getElementById('groundingModal').style.display = 'flex'; }
function closeGroundingModal() { document.getElementById('groundingModal').style.display = 'none'; }

/* 10. Counseling Modal */
function openCounselingModal() { document.getElementById('counselingModal').style.display = 'flex'; }
function closeCounselingModal() { document.getElementById('counselingModal').style.display = 'none'; }

function toggleNameFields() {
    const reqType = document.getElementById('requestType').value;
    document.getElementById('studentDetailsGroup').style.display = reqType === 'anonymous' ? 'none' : 'block';
}

async function handleCounselingSubmit(e) {
    e.preventDefault();
    const reqType = document.getElementById('requestType').value;
    const studentName = document.getElementById('studentName').value;
    const preferredMode = document.getElementById('preferredMode').value;
    const preferredDate = document.getElementById('preferredDate').value;
    const notes = document.getElementById('notes').value;

    try {
        const res = await fetch(SITE_ROOT + '/api/counseling.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                request_type: reqType,
                student_name: studentName,
                preferred_mode: preferredMode,
                preferred_date: preferredDate,
                notes: notes
            })
        });
        const data = await res.json();
        if (data.success) {
            alert(currentLang === 'si' 
                ? "ඔබගේ උපදේශන ඉල්ලීම සාර්ථකව යොමු කරන ලදී. කෙටි වේලාවකින් උපදේශකවරයෙකු ඔබ හා සම්බන්ධ වනු ඇත." 
                : "Your counseling request has been submitted successfully. A counselor will review it shortly.");
            closeCounselingModal();
            document.getElementById('counselingForm').reset();
        }
    } catch (err) {
        alert("Error submitting request: " + err.message);
    }
}

/* 11. Chatbot Widget */
function toggleChatbot() {
    const win = document.getElementById('chatbotWindow');
    win.style.display = (win.style.display === 'flex') ? 'none' : 'flex';
}

function sendQuickChip(text) {
    document.getElementById('chatInput').value = text;
    sendChatMessage();
}

function handleChatKeyPress(e) {
    if (e.key === 'Enter') sendChatMessage();
}

async function sendChatMessage() {
    const input = document.getElementById('chatInput');
    const msg = input.value.trim();
    if (!msg) return;

    const chatBody = document.getElementById('chatBody');
    chatBody.innerHTML += `<div class="chat-msg user">${msg}</div>`;
    input.value = '';
    chatBody.scrollTop = chatBody.scrollHeight;

    try {
        const res = await fetch(SITE_ROOT + '/api/chat.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: msg, lang: currentLang })
        });
        const data = await res.json();
        chatBody.innerHTML += `<div class="chat-msg bot">${data.reply}</div>`;
        chatBody.scrollTop = chatBody.scrollHeight;
    } catch (e) {
        chatBody.innerHTML += `<div class="chat-msg bot">සමාවන්න, සම්බන්ධතාවය බිඳ වැටුණි.</div>`;
    }
}

/* 12. Authentication Modals */
function openLoginModal() { document.getElementById('loginModal').style.display = 'flex'; }
function closeLoginModal() { document.getElementById('loginModal').style.display = 'none'; }
function openRegisterModal() {
    closeLoginModal();
    document.getElementById('registerModal').style.display = 'flex';
}
function closeRegisterModal() { document.getElementById('registerModal').style.display = 'none'; }
function switchToLogin() {
    closeRegisterModal();
    openLoginModal();
}

async function handleAjaxLogin(e) {
    e.preventDefault();
    const email = document.getElementById('loginEmail').value.trim();
    const password = document.getElementById('loginPassword').value;
    const errBox = document.getElementById('loginErrorMsg');

    try {
        const res = await fetch(SITE_ROOT + '/api/login.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email: email, password: password })
        });
        const data = await res.json();
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            errBox.innerText = data.message;
            errBox.style.display = 'block';
        }
    } catch (err) {
        errBox.innerText = "Connection error: " + err.message;
        errBox.style.display = 'block';
    }
}

async function handleAjaxRegister(e) {
    e.preventDefault();
    const fullname = document.getElementById('regFullname').value.trim();
    const studentId = document.getElementById('regStudentId').value.trim();
    const email = document.getElementById('regEmail').value.trim();
    const password = document.getElementById('regPassword').value;
    const errBox = document.getElementById('regErrorMsg');

    try {
        const res = await fetch(SITE_ROOT + '/api/register.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                fullname: fullname,
                student_id: studentId,
                email: email,
                password: password
            })
        });
        const data = await res.json();
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            errBox.innerText = data.message;
            errBox.style.display = 'block';
        }
    } catch (err) {
        errBox.innerText = "Registration error: " + err.message;
        errBox.style.display = 'block';
    }
}
