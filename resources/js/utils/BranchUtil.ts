export interface BranchInfo {
    name: string;
    shortName: string;
    city: string;
    location: string;
    code: string;
}

export class BranchUtil {
    public static getBranchInfo(): BranchInfo {
        // 1. Check explicit environment variable
        const envBranch = import.meta.env.VITE_BRANCH_NAME || import.meta.env.VITE_APP_BRANCH;
        if (envBranch) {
            const normalized = envBranch.toLowerCase();
            if (normalized.includes('davao')) {
                return {
                    name: 'Alcess Tech Davao Branch',
                    shortName: 'DAVAO BRANCH',
                    city: 'Davao City',
                    location: 'Alcess Tech, Davao City',
                    code: 'davao'
                };
            }
            if (normalized.includes('cebu')) {
                return {
                    name: 'Alcess Tech Cebu Branch',
                    shortName: 'CEBU BRANCH',
                    city: 'Cebu City',
                    location: 'Alcess Tech, Cebu City',
                    code: 'cebu'
                };
            }
            if (normalized.includes('cdo') || normalized.includes('cagayan')) {
                return {
                    name: 'Alcess Tech CDO Branch',
                    shortName: 'CDO BRANCH',
                    city: 'Cagayan de Oro City',
                    location: 'Alcess Tech, Cagayan de Oro City',
                    code: 'cdo'
                };
            }
            if (normalized.includes('gensan') || normalized.includes('general santos')) {
                return {
                    name: 'Alcess Tech Gensan Branch',
                    shortName: 'GENSAN BRANCH',
                    city: 'Gensan City',
                    location: 'Alcess Tech, Gensan City',
                    code: 'gensan'
                };
            }
            return {
                name: envBranch,
                shortName: envBranch.toUpperCase(),
                city: envBranch,
                location: envBranch,
                code: 'custom'
            };
        }

        // 2. Auto-detect from window location (port or hostname)
        if (typeof window !== 'undefined') {
            const port = window.location.port;
            const host = window.location.hostname.toLowerCase();

            if (port === '8001' || host.includes('davao')) {
                return {
                    name: 'Alcess Tech Davao Branch',
                    shortName: 'DAVAO BRANCH',
                    city: 'Davao City',
                    location: 'Alcess Tech, Davao City',
                    code: 'davao'
                };
            }
            if (port === '8002' || host.includes('cebu')) {
                return {
                    name: 'Alcess Tech Cebu Branch',
                    shortName: 'CEBU BRANCH',
                    city: 'Cebu City',
                    location: 'Alcess Tech, Cebu City',
                    code: 'cebu'
                };
            }
            if (port === '8003' || host.includes('cdo') || host.includes('cagayan')) {
                return {
                    name: 'Alcess Tech CDO Branch',
                    shortName: 'CDO BRANCH',
                    city: 'Cagayan de Oro City',
                    location: 'Alcess Tech, Cagayan de Oro City',
                    code: 'cdo'
                };
            }
        }

        // Default to GenSan
        return {
            name: 'Alcess Tech Gensan Branch',
            shortName: 'GENSAN BRANCH',
            city: 'Gensan City',
            location: 'Alcess Tech, Gensan City',
            code: 'gensan'
        };
    }

    public static getBranchName(): string {
        return this.getBranchInfo().name;
    }

    public static getBranchShortName(): string {
        return this.getBranchInfo().shortName;
    }

    public static getBranchCity(): string {
        return this.getBranchInfo().city;
    }

    public static getBranchLocation(): string {
        return this.getBranchInfo().location;
    }
}

export default BranchUtil;
